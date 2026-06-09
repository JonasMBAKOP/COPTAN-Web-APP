<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageRecipient extends Model
{
    protected $fillable = [
        'message_id',
        'recipient_id',
        'is_read',
        'read_at',
        'is_archived',
    ];

    protected function casts(): array
    {
        return [
            'is_read'     => 'boolean',
            'read_at'     => 'datetime',
            'is_archived' => 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}