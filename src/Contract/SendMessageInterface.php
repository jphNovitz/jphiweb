<?php

namespace App\Contract;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

interface SendMessageInterface
{
    public function __invoke(?string $email, ?string $phone, ?string $message, ?string $name = null);
}