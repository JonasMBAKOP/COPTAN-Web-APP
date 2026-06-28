<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentMessageRecipient extends Model
{
    protected $fillable = [
        'parent_message_id', 'student_id', 'phone_number', 'recipient_type',
        'sms_status', 'whatsapp_status', 'error_message', 'sent_at',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function parentMessage() { return $this->belongsTo(ParentMessage::class); }
    public function student() { return $this->belongsTo(Student::class); }
}