<?php


namespace App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw\Sign;


use App\Helpers\Filter;
use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    /**
     * @param Email $email
     * @param int $idTransaction
     * @param \DateTimeImmutable $createdAt
     * @param string $money
     * @return static
     */
    public static function profileWithdrawUser(
        Email $email,
        int $idTransaction,
       \DateTimeImmutable $createdAt,
        string $money
    ): self
    {
        $message = new self($email);
        $message->subject = "Заявка на вывод средств";
        $message->content = "Заявка (входящий №{$idTransaction}) от ".$createdAt->format("d.m.Y H:i").", на вывод денежных средств с виртуального счета, сумма: {$money}, принят в обработку Оператором. Сроки вывода, согласно Регламента, в течение 5 рабочих дней.";

        return $message;
    }


    public static function profileWithdrawModerator(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Вывод средств";
        $message->content = "Участник {$fullName}, подал заявление на вывод средств";

        return $message;
    }
}