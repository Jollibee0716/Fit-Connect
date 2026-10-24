<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMail
{
    /** Sends one email through Brevo's API. Returns null on success, or an error message. */
    public static function send(string $toEmail, string $toName, string $subject, string $html): ?string
    {
        $key = config('services.brevo.key');

        if (! $key) {
            return 'BREVO_API_KEY is missing. Check .env and run php artisan config:clear.';
        }

        $response = Http::withHeaders([
            'api-key' => $key,
            'accept'  => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender'      => [
                'name'  => config('mail.from.name'),
                'email' => config('mail.from.address'),
            ],
            'to'          => [['email' => $toEmail, 'name' => $toName]],
            'subject'     => $subject,
            'htmlContent' => $html,
        ]);

        return $response->successful() ? null : $response->body();
    }
}