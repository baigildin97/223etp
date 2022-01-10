<?php


namespace App\Model\Admin\UseCase\Users\Send;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function sendMessage(Email $email, string $text): self
    {
        $message = new self($email);
        $message->subject = "Сообщение от администратора";
        $message->content = $text;

        return $message;
    }
}