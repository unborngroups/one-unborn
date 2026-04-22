<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PrivateChatController extends Controller
{
    // Fetch messages between authenticated user and another user
    public function fetchMessages(Request $request, $userId)
    {
        $authId = Auth::id();
        $messages = PrivateMessage::where(function($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)->where('receiver_id', $userId);
            })->orWhere(function($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json($messages);
    }

    // Send a private message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:5120', // 5MB
        ]);

        $data = [
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $destinationPath = public_path('images/online_chat');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $filename);
            $relativePath = 'images/online_chat/' . $filename;
            $data['file_path'] = $relativePath;
            $data['file_type'] = $file->getClientMimeType();
            $data['original_name'] = $file->getClientOriginalName();
        }

        $msg = PrivateMessage::create($data);
        return response()->json($msg);
    }

    // List all users except self (for demo/testing)
    public function onlineUsers()
    {
        $authId = Auth::id();
        $users = User::where('id', '!=', $authId)
            ->select('id', 'name')
            ->with(['status'])
            ->get()
            ->map(function($user) {
                $user->is_online = $user->status && $user->status->is_online;
                $user->last_seen = $user->status ? $user->status->last_seen : null;
                unset($user->status);
                return $user;
            });
        return response()->json($users);
    }
}
