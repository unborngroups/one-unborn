<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'company_id',
        'sender',
        'subject',
        'body',
        'attachment_path',
        'status',
        'error_message',
        'file_hash',
        'source',
        'is_active',
    ];
}
