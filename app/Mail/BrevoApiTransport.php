<?php

namespace App\Mail;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\MessageConverter;

class BrevoApiTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $originalMessage = $message->getOriginalMessage();

        // Ensure we have a Message instance, not just RawMessage
        if (! $originalMessage instanceof Message) {
            throw new \InvalidArgumentException('Message must be an instance of Symfony\Component\Mime\Message');
        }

        $email = MessageConverter::toEmail($originalMessage);

        $config = Configuration::getDefaultConfiguration()->setApiKey(
            'api-key',
            config('services.brevo.key')
        );

        $api = new TransactionalEmailsApi(
            new \GuzzleHttp\Client(['timeout' => 10, 'connect_timeout' => 5]),
            $config
        );

        $to = [];
        foreach ($email->getTo() as $address) {
            $to[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?: $address->getAddress(),
            ];
        }

        $from = $email->getFrom()[0];

        $sendEmail = new SendSmtpEmail([
            'sender' => [
                'email' => $from->getAddress(),
                'name' => $from->getName() ?: $from->getAddress(),
            ],
            'to' => $to,
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
        ]);

        $result = $api->sendTransacEmail($sendEmail);
        Log::info('✅ Brevo email sent', ['message_id' => $result->getMessageId()]);
    }

    public function __toString(): string
    {
        return 'brevo-api';
    }
}
