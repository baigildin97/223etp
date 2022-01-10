<?php
declare(strict_types=1);

namespace App\Model\Admin\UseCase\Users\Lock;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function notifyAccountLocked(Email $email, string $cause): self
    {
        $message = new self($email);
        $message->subject = "Аккаунт заблокирован";
        $message->content = "Ваш аккаунт был заблокирован по причине: {$cause}";

        return $message;
    }

    public static function notifyAccountUnlocked(Email $email, string $cause): self
    {
        $message = new self($email);
        $message->subject = "Аккаунт разблокирован";
        $message->content = "Ваш аккаунт был разблокирован. Сообщение модератора: {$cause}";

        return $message;
    }
}