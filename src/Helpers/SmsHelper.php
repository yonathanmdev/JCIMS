<?php
namespace App\Helpers;

class SmsHelper
{
    public static function send(string $phoneNumber, string $message): bool
    {
        $ch = curl_init($_ENV['SMS_API_URL']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'KEY: ' . $_ENV['SMS_API_KEY'],
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'msisdn' => $phoneNumber,
                'text' => $message,
            ]),
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("SMS send error: " . $error);
            return false;
        }

        return true;
    }
}