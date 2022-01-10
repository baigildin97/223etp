<?php


namespace App\Model\Work\Procedure\UseCase\Protocol\Sign\Winner;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function organizerSignedProtocolWinner(Email $email, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Протокол подписан";
        $message->content = "Победитель подписал протокол о результатах торгов по торговой
                             процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>.";

        return $message;
    }

    public static function participantSignedProtocolWinner(Email $email, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Протокол подписан";
        $message->content = "Вы подписали протокол о результатах торгов по торговой 
                             процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>.";

        return $message;
    }
}