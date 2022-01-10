<?php


namespace App\Model\User\UseCase\Profile\Payment\Transaction\Confirm;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function  virtualBalanceReplenished(Email $email, string $sum): self
    {
        $message = new self($email);
        $message->subject = "Пополнение баланса";
        $message->content = "Баланс вашего виртуального счета пополнен на сумму {$sum}";

        return $message;
    }

    public static function virtualBalanceReplenishedModerator(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Пополнение баланса";
        $message->content = "Заявление на зачисление средств участника {$fullName}, исполнено";

        return $message;

    }

    public static function  virtualBalanceWithdraw(Email $email, string $idNumber): self
    {
        $message = new self($email);
        $message->subject = "Вывод средств";
        $message->content = "Заявление №{$idNumber} на вывод средств, принято";

        return $message;
    }

    public static function virtualBalanceWithdrawModerator(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Вывод средств";
        $message->content = "Заявление на вывод средств участника {$fullName}, исполнено";

        return $message;
    }
}