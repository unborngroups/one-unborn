<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Only Superadmin/Admin (user_type_id 1 or 2) can view chat logs.
        if (! in_array($user->user_type_id, [1, 2], true)) {
            abort(403, 'You are not allowed to view chat logs.');
        }

        $messages = ChatMessage::query()
            ->with(['sender'])
            ->latest('id')
            ->paginate(50);

        return view('chat.logs', [
            'messages' => $messages,
        ]);
    }
}
