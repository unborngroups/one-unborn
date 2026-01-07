<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAttachment extends Model
{
    protected $table = 'chat_attachments';

    protected $fillable = [
        'chat_message_id',
        'file_path',
        'file_type',
        'original_name',
    ];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }
}
