<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Queue\Queueable;
use App\Models\ParentMessage;
use App\Models\ParentMessageRecipient;
use App\Services\Notification\NotificationGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendParentMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ParentMessage $parentMessage)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationGateway $gateway): void
    {
        $this->parentMessage->update(['status' => 'sending']);

        $sent = 0;
        $failed = 0;

        foreach ($this->parentMessage->recipients as $recipient) {
            $anySuccess = false;
            $errors = [];

            if (in_array($this->parentMessage->channel, ['sms', 'both'])) {
                $result = $gateway->sendSms($recipient->phone_number, $this->parentMessage->body);
                $recipient->sms_status = $result['success'] ? 'sent' : 'failed';
                if (!$result['success']) $errors[] = 'SMS: ' . $result['error'];
                if ($result['success']) $anySuccess = true;
            } else {
                $recipient->sms_status = 'skipped';
            }

            if (in_array($this->parentMessage->channel, ['whatsapp', 'both'])) {
                $result = $gateway->sendWhatsApp($recipient->phone_number, $this->parentMessage->body);
                $recipient->whatsapp_status = $result['success'] ? 'sent' : 'failed';
                if (!$result['success']) $errors[] = 'WhatsApp: ' . $result['error'];
                if ($result['success']) $anySuccess = true;
            } else {
                $recipient->whatsapp_status = 'skipped';
            }

            $recipient->error_message = !empty($errors) ? implode(' | ', $errors) : null;
            $recipient->sent_at = now();
            $recipient->save();

            $anySuccess ? $sent++ : $failed++;
        }

        $this->parentMessage->update([
            'sent_count'   => $sent,
            'failed_count' => $failed,
            'status'       => 'completed',
        ]);
    }
}
