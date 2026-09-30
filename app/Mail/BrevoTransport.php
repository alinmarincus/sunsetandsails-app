<?php

namespace App\Mail;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

/**
 * Trimite emailurile clubului prin API-ul Brevo, nu prin SMTP.
 *
 * Motivul: folosim aceeasi cheie ca in configurator, deci o singura
 * credentiala de administrat; iar hostingurile partajate blocheaza des
 * porturile SMTP spre exterior, in timp ce un apel HTTPS trece mereu.
 */
class BrevoTransport extends AbstractTransport
{
    public function __construct(
        private readonly string $apiKey,
        private readonly int $timeout = 15,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $payload = array_filter([
            'sender'      => $this->address($email->getFrom()[0] ?? null),
            'to'          => $this->addresses($email->getTo()),
            'cc'          => $this->addresses($email->getCc()),
            'bcc'         => $this->addresses($email->getBcc()),
            'replyTo'     => $this->address($email->getReplyTo()[0] ?? null),
            'subject'     => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ]);

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => [
                'accept: application/json',
                'api-key: ' . $this->apiKey,
                'content-type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Brevo: conexiunea a esuat — ' . $curlErr);
        }

        if ($status >= 400) {
            throw new \RuntimeException("Brevo a raspuns {$status}: {$response}");
        }
    }

    /** @param Address[] $addresses */
    private function addresses(array $addresses): array
    {
        return array_values(array_filter(array_map(
            fn (Address $a) => $this->address($a),
            $addresses
        )));
    }

    private function address(?Address $address): ?array
    {
        if (! $address) {
            return null;
        }

        return array_filter([
            'email' => $address->getAddress(),
            'name'  => $address->getName() ?: null,
        ]);
    }

    public function __toString(): string
    {
        return 'brevo://api.brevo.com';
    }
}
