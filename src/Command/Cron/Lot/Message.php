<?php


namespace App\Command\Cron\Lot;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function organizerProtocolSummingRegProcedure(Email $email, string $procedureFullNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Необходимо подписать протокол";
        $message->content = "Необходимо подписать протокол о подведении итогов приема и регистрации заявок
                             по торговой процедуре <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a>.";

        return $message;
    }
}