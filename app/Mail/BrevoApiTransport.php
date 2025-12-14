<?php
namespace App\Mail;

use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Log;

class BrevoApiTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
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
                'name' => $address->getName() ?: $address->getAddress()
            ];
        }

        $from = $email->getFrom()[0];
        
        $sendEmail = new SendSmtpEmail([
            'sender' => [
                'email' => $from->getAddress(),
                'name' => $from->getName() ?: $from->getAddress()
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
