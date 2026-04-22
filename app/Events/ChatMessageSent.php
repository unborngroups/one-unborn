<?php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

class ChatMessageSent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message->load(['sender.profile', 'attachments']);
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat-group.' . $this->message->chat_group_id);
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->transformMessage($this->message),
        ];
    }

    protected function transformMessage(ChatMessage $message): array
    {
        $sender = $message->sender;

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return [
            'id' => $message->id,
            'chat_group_id' => $message->chat_group_id,
            'message' => $message->message,
            'created_at' => $message->created_at?->toIso8601String(),
            'sender' => $sender ? [
                'id' => $sender->id,
                'name' => $sender->name,
                'avatar' => $sender->profile && $sender->profile->profile_photo
                    ? $disk->url($sender->profile->profile_photo)
                    : null,
            ] : null,
            'attachments' => $message->attachments->map(function ($attachment) {
                /** @var FilesystemAdapter $disk */
                $disk = Storage::disk('public');
                return [
                    'id' => $attachment->id,
                    'name' => $attachment->original_name,
                    'type' => $attachment->file_type,
                    'url' => $disk->url($attachment->file_path),
                ];
            })->all(),
        ];
    }
}
