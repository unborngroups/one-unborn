<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverableEmailLog extends Model
{
    protected $fillable = [
        'deliverable_id',
        'sent_by',
        'sent_at',
        'sent_from_email',
        'sent_to_email',
        'subject',
        'body',
        'attachment_path',
        'received_at',
        'received_status',
    ];

    // Relationships
    public function deliverable()
    {
        return $this->belongsTo(Deliverables::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
