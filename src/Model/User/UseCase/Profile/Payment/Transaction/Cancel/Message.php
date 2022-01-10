<?php


namespace App\Model\User\UseCase\Profile\Payment\Transaction\Cancel;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function virtualBalanceReject(Email $email, string $idNumber): self
    {
        $message = new self($email);
        $message->subject = "Заявление отклонено";
        $message->content = "Заявление №{$idNumber} на вывод средств, отклонено";

        return $message;
    }

    public static function virtualBalanceRejectModerator(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Заявление отклонено";
        $message->content = "Заявление на вывод средств участника {$fullName}, отклонено";

        return $message;
    }
}