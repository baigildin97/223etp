<?php


namespace App\Command\Cron\Auction;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function organizerCompletedAuction(Email $email, int $procedureFullNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Торги завершены";
        $message->content = "Торги по процедуре <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a> завершены, Вам необходимо подписать протокол о результатах торгов";

        return $message;
    }

    public static function completedAuction(Email $email, int $procedureFullNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Торги завершены";
        $message->content = "Торги по процедуре <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a> завершены.";

        return $message;
    }

    public static function startAuction(Email $email, string $showProcedureUrl, int $procedureId): self{
        $message = new self($email);
        $message->subject = "Торги начались";
        $message->content = "Торги по процедуре <a href=\"{$showProcedureUrl}\">№{$procedureId}</a> начались";

        return $message;
    }
}