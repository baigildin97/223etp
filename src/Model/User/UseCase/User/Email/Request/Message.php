<?php


namespace App\Model\User\UseCase\User\Email\Request;


use App\Model\User\Entity\User\Email;
use App\Services\Notification\EmailMessageBase;

class Message extends EmailMessageBase
{
    public static function userEmailReset(Email $email, string $fullName, string $clientIP, string $confirmURL): self
    {
        $message = new self($email);
        $message->subject = "Смена email адреса";
        $message->content = "Уважаемый {$fullName}! Для подтверждения запроса на смену email с 
        IP {$clientIP} перейдите по ссылке {$confirmURL}. Ссылка действительна в течение 2-х часов.";
    }
}