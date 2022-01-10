<?php


namespace App\Model\User\UseCase\User\Reset\Request;


use App\Model\User\Entity\User\Email;
use App\Services\Notification\EmailMessageBase;

class Message extends EmailMessageBase
{
    public static function userResetPassword(Email $email, string $confirmURL): self
    {
        $message = new self($email);
        $message->subject = "Сброс пароля";
        $message->content = "Вами был отправлен запрос на сброс пароля на площадке РесТорг.
         Для подтверждения перейдите по ссылке: {$confirmURL}";
        return $message;
    }
}