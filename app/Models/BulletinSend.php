<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulletinSend extends Model
{
    protected $fillable = [
        'bulletin_report_id', 'student_enrollment_id', 'sent_by',
        'phone_number', 'status', 'error_message', 'sent_at',
    ];

    protected function casts(): array { return ['sent_at' => 'datetime']; }

    public function studentEnrollment() { return $this->belongsTo(StudentEnrollment::class); }
    public function sentBy() { return $this->belongsTo(User::class, 'sent_by'); }
}