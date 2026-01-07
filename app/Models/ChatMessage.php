<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['chat_group_id', 'sender_id', 'message', 'client_token'];

    public function sender() {
        return $this->belongsTo(User::class,'sender_id');
    }

    public function attachments() {
        return $this->hasMany(ChatAttachment::class);
    }
}
