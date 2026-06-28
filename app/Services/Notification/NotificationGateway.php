<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationGateway
{
    private bool $simulationMode;

    public function __construct()
    {
        // Si les clés API ne sont pas configurées, on passe en simulation
        $this->simulationMode = empty(config('services.twilio.sid'))
            || empty(config('services.twilio.token'));
    }

    /**
     * Envoie un SMS. Retourne ['success' => bool, 'error' => string|null]
     */
    public function sendSms(string $phone, string $message): array
    {
        $phone = $this->normalizePhone($phone);

        if ($this->simulationMode) {
            Log::info("[SIMULATION SMS] À {$phone} : {$message}");
            return ['success' => true, 'error' => null, 'simulated' => true];
        }

        try {
            $response = Http::asForm()->withBasicAuth(
                config('services.twilio.sid'),
                config('services.twilio.token')
            )->post(
                'https://api.twilio.com/2010-04-01/Accounts/'
                . config('services.twilio.sid') . '/Messages.json',
                [
                    'From' => config('services.twilio.sms_from'),
                    'To'   => $phone,
                    'Body' => $message,
                ]
            );

            if ($response->successful()) {
                return ['success' => true, 'error' => null];
            }
            return ['success' => false, 'error' => $response->body()];

        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Envoie un message WhatsApp (texte)
     */
    public function sendWhatsApp(string $phone, string $message): array
    {
        $phone = $this->normalizePhone($phone);

        if ($this->simulationMode) {
            Log::info("[SIMULATION WHATSAPP] À {$phone} : {$message}");
            return ['success' => true, 'error' => null, 'simulated' => true];
        }

        try {
            $response = Http::asForm()->withBasicAuth(
                config('services.twilio.sid'),
                config('services.twilio.token')
            )->post(
                'https://api.twilio.com/2010-04-01/Accounts/'
                . config('services.twilio.sid') . '/Messages.json',
                [
                    'From' => 'whatsapp:' . config('services.twilio.whatsapp_from'),
                    'To'   => 'whatsapp:' . $phone,
                    'Body' => $message,
                ]
            );

            if ($response->successful()) {
                return ['success' => true, 'error' => null];
            }
            return ['success' => false, 'error' => $response->body()];

        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Envoie un document PDF (bulletin) par WhatsApp
     */
    public function sendWhatsAppDocument(string $phone, string $mediaUrl, string $caption = ''): array
    {
        $phone = $this->normalizePhone($phone);

        if ($this->simulationMode) {
            Log::info("[SIMULATION WHATSAPP DOC] À {$phone} : {$mediaUrl}");
            return ['success' => true, 'error' => null, 'simulated' => true];
        }

        try {
            $response = Http::asForm()->withBasicAuth(
                config('services.twilio.sid'),
                config('services.twilio.token')
            )->post(
                'https://api.twilio.com/2010-04-01/Accounts/'
                . config('services.twilio.sid') . '/Messages.json',
                [
                    'From'      => 'whatsapp:' . config('services.twilio.whatsapp_from'),
                    'To'        => 'whatsapp:' . $phone,
                    'Body'      => $caption,
                    'MediaUrl'  => $mediaUrl,
                ]
            );

            if ($response->successful()) {
                return ['success' => true, 'error' => null];
            }
            return ['success' => false, 'error' => $response->body()];

        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function isSimulationMode(): bool
    {
        return $this->simulationMode;
    }

    /** Vérifie si un numéro a potentiellement un compte WhatsApp (formalité visuelle, pas de vraie vérif API gratuite) */
    public function hasLikelyWhatsApp(string $phone): bool
    {
        $phone = $this->normalizePhone($phone);
        return strlen($phone) >= 9; // simplification : tout numéro valide est éligible
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^\d+]/', '', $phone);
        if (!str_starts_with($phone, '+')) {
            // Cameroun par défaut
            $phone = str_starts_with($phone, '237') ? '+' . $phone : '+237' . $phone;
        }
        return $phone;
    }
}