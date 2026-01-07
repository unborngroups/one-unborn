<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\ChatGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use App\Events\ChatMessageSent;
use App\Services\LiveChatService;
use Illuminate\Filesystem\FilesystemAdapter;

class ChatController extends Controller
{
    protected LiveChatService $chatService;

    public function __construct(LiveChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    // Real-time typing event
    public function typing(Request $request)
    {
        $group = $this->resolveGroup($request);

        broadcast(new \App\Events\TypingEvent($group->id))->toOthers();
        return response()->json(['status' => 'ok']);
    }

    public function fetchMessages(Request $request)
    {
        $group = $this->resolveGroup($request);
        $limit = max(1, min(200, (int) $request->query('limit', 50)));
        $sinceId = (int) $request->query('since_id', 0);

        $query = ChatMessage::query()
            ->where('chat_group_id', $group->id)
            ->with(['sender.profile', 'attachments']);

        if ($sinceId > 0) {
            // Incremental fetch: only messages created after the given ID.
            $query->where('id', '>', $sinceId)->orderBy('id', 'asc');
        } else {
            // Initial history: latest N messages, ordered from oldest to newest.
            $query->latest()->take($limit);
        }

        $messages = $query->get();

        if ($sinceId === 0) {
            $messages = $messages->reverse()->values();
        }

        $payload = $messages
            ->map(fn (ChatMessage $message) => $this->formatMessage($message))
            ->values();

        return response()->json($payload);
    }


    public function send(Request $request)
    {
        Log::info('chat.send invoked', [
            'user_id' => Auth::id(),
            'group_id' => $request->input('group_id'),
            'has_message' => $request->filled('message'),
            'has_file' => $request->hasFile('attachment'),
        ]);

        // Quick guard to log and fail fast if no group id present
        if (! $request->input('group_id')) {
            Log::warning('chat.send missing group_id', [
                'user_id' => Auth::id(),
                'payload' => $request->all(),
            ]);
        }

        $group = $this->resolveGroup($request, null, true);

        $validated = $request->validate([
            'message' => 'nullable|string|max:5000',
            'group_id' => 'required|integer',
            'client_token' => 'nullable|string|max:64',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if (! $request->filled('message') && ! $request->hasFile('attachment')) {
            throw ValidationException::withMessages([
                'message' => __('Please type a message or attach a file.'),
            ]);
        }

        $messageText = isset($validated['message']) ? trim((string) $validated['message']) : null;

        // Defensive guard: make the send operation idempotent using a
        // client-generated token. If the browser accidentally fires
        // the same request multiple times with the same token, we
        // will always reuse the same ChatMessage row instead of
        // creating duplicates.
        $clientToken = $validated['client_token'] ?? null;

        $msg = null;
        $isDuplicate = false;

        if ($clientToken) {
            $msg = ChatMessage::firstOrCreate(
                ['client_token' => $clientToken],
                [
                    'chat_group_id' => $group->id,
                    'sender_id' => Auth::id(),
                    'message' => $messageText,
                ]
            );

            if (! $msg->wasRecentlyCreated) {
                Log::info('chat.send reused message via client_token', [
                    'message_id' => $msg->id,
                    'group_id' => $group->id,
                    'sender_id' => Auth::id(),
                    'client_token' => $clientToken,
                ]);
                $isDuplicate = true;
            }
        } else {
            // Fallback: best-effort dedupe by recent identical text so
            // that even older clients without client_token are less
            // likely to create duplicate rows on rapid double-submit.
            if ($messageText !== null && $messageText !== '') {
                $recentDuplicate = ChatMessage::query()
                    ->where('chat_group_id', $group->id)
                    ->where('sender_id', Auth::id())
                    ->where('message', $messageText)
                    ->where('created_at', '>=', now()->subSeconds(10))
                    ->latest('id')
                    ->first();

                if ($recentDuplicate) {
                    Log::info('chat.send detected recent duplicate by text', [
                        'message_id' => $recentDuplicate->id,
                        'group_id' => $group->id,
                        'sender_id' => Auth::id(),
                    ]);
                    $msg = $recentDuplicate;
                    $isDuplicate = true;
                }
            }

            if (! $msg) {
                $msg = ChatMessage::create([
                    'chat_group_id' => $group->id,
                    'sender_id' => Auth::id(),
                    'message' => $messageText,
                ]);
            }
        }

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('chat_attachments', 'public');
            $msg->attachments()->create([
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        $msg->load(['sender.profile', 'attachments']);

        // Mark sender as online in cache (presence TTL).
        Cache::put('chat-online-user-' . Auth::id(), now(), 120);

        $payload = $this->formatMessage($msg);

        // Log the final outcome of this send so we can
        // distinguish between true duplicates (multiple
        // database rows) and UI-level duplication.
        Log::info('chat.send result', [
            'message_id' => $msg->id,
            'group_id' => $msg->chat_group_id,
            'sender_id' => $msg->sender_id,
            'client_token' => $clientToken,
            'is_duplicate' => $isDuplicate,
            'has_attachments' => $msg->attachments->isNotEmpty(),
        ]);

        // Only broadcast when this is not a recently detected
        // duplicate message. The original send already broadcasted
        // the event, so skipping here prevents the same message from
        // appearing twice in other users' chat windows.
        if (! $isDuplicate) {
            broadcast(new \App\Events\ChatMessageSent($msg))->toOthers();
        }

        return response()->json($payload);
    }

    public function onlineUsers(Request $request, $groupId)
    {
        $group = $this->resolveGroup($request, (int) $groupId);

        // Mark the requesting user as online in cache so that
        // other users will see them as online for a short period.
        Cache::put('chat-online-user-' . $request->user()->id, now(), 120);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        try {
            $users = $group->users()
                ->with(['status', 'profile'])
                ->get()
                ->map(function (User $user) use ($disk) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'is_online' => Cache::has('chat-online-user-' . $user->id),
                        'last_seen' => optional($user->status)->last_seen,
                        'avatar' => $user->profile && $user->profile->profile_photo
                            ? $disk->url($user->profile->profile_photo)
                            : null,
                    ];
                });
        } catch (\Throwable $e) {
            // If the optional user_statuses table or related model is
            // missing in this deployment, gracefully fall back to a
            // simpler online snapshot without last_seen data instead
            // of failing the entire endpoint.
            Log::error('chat.onlineUsers fallback without status', [
                'group_id' => $group->id,
                'error' => $e->getMessage(),
            ]);

            $users = $group->users()
                ->with(['profile'])
                ->get()
                ->map(function (User $user) use ($disk) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'is_online' => Cache::has('chat-online-user-' . $user->id),
                        'last_seen' => null,
                        'avatar' => $user->profile && $user->profile->profile_photo
                            ? $disk->url($user->profile->profile_photo)
                            : null,
                    ];
                });
        }

        Log::info('chat.onlineUsers snapshot', [
            'request_user_id' => $request->user()->id,
            'group_id' => $group->id,
            'users' => $users,
        ]);

        return response()->json($users);
    }

    public function bootstrap(Request $request)
    {
        $user = $request->user();
        Log::info('chat.bootstrap invoked', [
            'user_id' => optional($user)->id,
        ]);
        $user->loadMissing(['profile', 'userType']);

        // Mark user online when chat boots.
        Cache::put('chat-online-user-' . $user->id, now(), 120);
        $groups = $this->chatService->syncUserGroups($user);
        Log::info('chat.bootstrap groups', [
            'user_id' => $user->id,
            'group_ids' => $groups->pluck('id')->all(),
        ]);
        $activeGroupId = (int) ($request->query('group_id') ?: ($groups->first()->id ?? 0));
        $limit = max(1, min(100, (int) $request->query('limit', 30)));

        $messages = [];

        if ($activeGroupId) {
            $messages = ChatMessage::query()
                ->where('chat_group_id', $activeGroupId)
                ->with(['sender.profile', 'attachments'])
                ->latest()
                ->take($limit)
                ->get()
                ->reverse()
                ->values()
                ->map(fn (ChatMessage $message) => $this->formatMessage($message))
                ->all();
        }

        return response()->json([
            'user' => $this->formatUser($user),
            'groups' => $groups->map(fn (ChatGroup $group) => $this->formatGroup($group))->values()->all(),
            'active_group_id' => $activeGroupId,
            'messages' => $messages,
        ]);
    }

    protected function formatMessage(ChatMessage $message): array
    {
        $sender = $message->sender;

        if ($sender) {
            $sender->loadMissing(['profile', 'userType']);
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return [
            'id' => $message->id,
            'chat_group_id' => $message->chat_group_id,
            'message' => $message->message,
            'created_at' => $message->created_at?->toIso8601String(),
            'sender' => $sender ? $this->formatUser($sender) : null,
            'attachments' => $message->attachments->map(function ($attachment) use ($disk) {
                return [
                    'id' => $attachment->id,
                    'name' => $attachment->original_name,
                    'type' => $attachment->file_type,
                    'url' => $disk->url($attachment->file_path),
                    'file_path' => $attachment->file_path,
                    'original_name' => $attachment->original_name,
                ];
            })->all(),
        ];
    }

    protected function formatGroup(ChatGroup $group): array
    {
        return [
            'id' => $group->id,
            'name' => $group->name,
            'company_id' => $group->company_id,
            'company_name' => optional($group->company)->company_name,
        ];
    }

    protected function formatUser(User $user): array
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $avatar = $user->profile && $user->profile->profile_photo
            ? $disk->url($user->profile->profile_photo)
            : null;
        $profilePhoto = $user->profile ? $user->profile->profile_photo : null;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $avatar,
            'type' => $user->userType->name ?? null,
            'profile' => $profilePhoto ? ['profile_photo' => $profilePhoto] : null,
        ];
    }

    protected function resolveGroup(Request $request, ?int $implicitId = null, bool $ensureSync = false): ChatGroup
    {
        $groupId = $implicitId
            ?? (int) ($request->route('chatGroup')
                ?? $request->route('group')
                ?? $request->route('id')
                ?? $request->input('group_id'));

        if (! $groupId) {
            abort(422, 'Chat group is required.');
        }

        $group = ChatGroup::with('company')->findOrFail($groupId);
        $user = $request->user();

        if (! $this->chatService->userCanAccessGroup($user, $group)) {
            abort(403, 'You are not allowed to access this chat group.');
        }

        if ($ensureSync) {
            $group->users()->syncWithoutDetaching([$user->id]);
        }

        return $group;
    }
}
