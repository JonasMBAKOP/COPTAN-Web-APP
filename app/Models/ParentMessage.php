<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentMessage extends Model
{
    protected $fillable = [
        'sender_id', 'subject', 'body', 'channel', 'target_type',
        'class_group_id', 'total_recipients', 'sent_count',
        'failed_count', 'status',
    ];

    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function classGroup() { return $this->belongsTo(ClassGroup::class); }
    public function recipients() { return $this->hasMany(ParentMessageRecipient::class); }
}