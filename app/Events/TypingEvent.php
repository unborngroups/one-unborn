<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;

class TypingEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $userId;
    public $userName;
    public $groupId;

    public function __construct($groupId)
    {
        $this->userId = Auth::id();
        $this->userName = Auth::user()->name;
        $this->groupId = $groupId;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat-group.' . $this->groupId);
    }
}
