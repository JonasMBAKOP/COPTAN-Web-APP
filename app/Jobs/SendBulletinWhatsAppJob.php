<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Queue\Queueable;
use App\Models\BulletinSend;
use App\Services\Notification\NotificationGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulletinWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public BulletinSend $bulletinSend,
        public string $pdfUrl,
        public string $studentName
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationGateway $gateway): void
    {
        $caption = "Bonjour, voici le bulletin de {$this->studentName}. "
                 . "Merci de bien vouloir consulter le document ci-joint.";

        $result = $gateway->sendWhatsAppDocument(
            $this->bulletinSend->phone_number, $this->pdfUrl, $caption
        );

        $this->bulletinSend->update([
            'status'        => $result['success'] ? 'sent' : 'failed',
            'error_message' => $result['error'],
            'sent_at'       => now(),
        ]);
    }
}
