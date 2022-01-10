<?php


namespace App\Model\Work\Procedure\UseCase\Sign;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function procedureModerateRepeat(Email $email, int $idNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Процедура ожидает модерации";
        $message->content = "Процедура №{$idNumber} ожидает повторной модерации";

        return $message;
    }

    public static function procedureModerate(Email $email, int $idNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Процедура ожидает модерации";
        $message->content = "Ваше заявление на размещение процедуры торгов ожидает модерации. Процедуре торгов присвоен <a href=\"{$showProcedureUrl}\">№{$idNumber}</a>";

        return $message;
    }

    public static function procedureCreate(Email $email, int $idNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Создана новая процедура";
        $message->content = "Создана новая процедура <a href=\"{$showProcedureUrl}\">№{$idNumber}</a>";

        return $message;
    }
}