<?php

namespace App\Service;

use App\Contract\SendMessageInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SendMessage implements SendMessageInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(?string $email = '', ?string $phone = '', ?string $message = '', ?string $name = null): void
    {
        $lines = array_filter([$name, $email, $phone]);

        try {
            $mail = (new Email())
                ->from($_ENV['MAIL_FROM'])
                ->to($_ENV['MAIL_TO'])
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Nouveau message de la page de contact')
                ->text(implode("\n", $lines) . "\n\n" . $message)
                ->html(implode('', array_map(
                    fn (string $line) => '<p>' . htmlspecialchars($line) . '</p>',
                    $lines
                )) . '<p>' . nl2br(htmlspecialchars((string) $message)) . '</p>');

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mail->replyTo(new Address($email, (string) $name));
            }

            $this->mailer->send($mail);
        } catch (TransportExceptionInterface $e) {
            // Handle the exception (e.g., log it)
//            dd($e);
            throw new TransportException('Error sending email: ');
        }


    }
}