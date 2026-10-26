<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class XenditService
{
    public function createCheckout(
        string $reference,
        float $amountPesos,
        string $email,
        string $description,
        string $customerName = 'FitConnect Member'
    ): array {

        /*
        |--------------------------------------------------------------------------
        | 1. Xendit Configuration
        |--------------------------------------------------------------------------
        */

        $secret = config('services.xendit.secret_key');

        $apiUrl = rtrim(
            config(
                'services.xendit.api_url'
            ) ?: 'https://api.xendit.co',
            '/'
        );

        if (empty($secret)) {
            throw new RuntimeException(
                'Xendit API key is not configured.'
            );
        }

        if ($amountPesos <= 0) {
            throw new RuntimeException(
                'Payment amount must be greater than zero.'
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException(
                'Invalid customer email address.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Prepare Customer Name
        |--------------------------------------------------------------------------
        */

        $cleanName = preg_replace(
            '/[^a-zA-Z0-9 ]/',
            '',
            $customerName
        );

        $cleanName = trim($cleanName);

        if ($cleanName === '') {
            $cleanName = 'FitConnect Member';
        }

        // Correct regular expression.
        $parts = preg_split('/\s+/', $cleanName);

        $givenName = substr(
            $parts[0] ?? 'Member',
            0,
            50
        );

        $surname = substr(
            count($parts) > 1
                ? implode(' ', array_slice($parts, 1))
                : '',
            0,
            50
        );

        $individualDetail = [
            'given_names' => $givenName,
        ];

        if ($surname !== '') {
            $individualDetail['surname'] = $surname;
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Prepare Xendit Payment Session
        |--------------------------------------------------------------------------
        */

        $payload = [
            'reference_id' => $reference,

            'session_type' => 'PAY',

            'mode' => 'PAYMENT_LINK',

            'amount' => round($amountPesos, 2),

            'currency' => 'PHP',

            'country' => 'PH',

            'customer' => [
                'reference_id' => 'FCUSER' .
                    preg_replace(
                        '/[^a-zA-Z0-9]/',
                        '',
                        $reference
                    ),

                'type' => 'INDIVIDUAL',

                'email' => $email,

                'individual_detail' => $individualDetail,
            ],

            'description' => $description,
        ];

        /*
        |--------------------------------------------------------------------------
        | 4. Configure HTTPS Return URLs
        |--------------------------------------------------------------------------
        |
        | Xendit rejects HTTP return URLs.
        |
        | On localhost, do not send success/cancel URLs.
        | Xendit can use its default hosted return page.
        |
        | When deployed with HTTPS, include the dashboard URLs.
        */

        $appUrl = rtrim(
            config('app.url') ?: '',
            '/'
        );

        if (
            filter_var($appUrl, FILTER_VALIDATE_URL) &&
            strtolower(
                parse_url($appUrl, PHP_URL_SCHEME) ?? ''
            ) === 'https'
        ) {
            $payload['success_return_url'] =
                $appUrl .
                '/member/dashboard?payment=processing';

            $payload['cancel_return_url'] =
                $appUrl .
                '/member/dashboard?payment=cancelled';
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Send Request To Xendit
        |--------------------------------------------------------------------------
        */

        $response = Http::withBasicAuth(
                $secret,
                ''
            )
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->post(
                $apiUrl . '/sessions',
                $payload
            );

        /*
        |--------------------------------------------------------------------------
        | 6. Handle Xendit Errors
        |--------------------------------------------------------------------------
        */

        if ($response->failed()) {
            $message = $response->json(
                'message',
                'Request was rejected.'
            );

            if (!is_string($message)) {
                $message = 'Request was rejected.';
            }

            throw new RuntimeException(
                'Xendit checkout error (HTTP ' .
                $response->status() .
                '). ' .
                $message
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Validate Checkout Response
        |--------------------------------------------------------------------------
        */

        $data = $response->json();

        if (
            !is_array($data) ||
            empty($data['payment_session_id']) ||
            empty($data['payment_link_url'])
        ) {
            throw new RuntimeException(
                'Xendit returned incomplete checkout details.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Return Checkout Details
        |--------------------------------------------------------------------------
        */

        return [
            'session_id' => $data['payment_session_id'],

            'checkout_url' => $data['payment_link_url'],

            'status' => $data['status'] ?? 'ACTIVE',
        ];
    }
}
