<?php


namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Action;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function organizerRejectBid(Email $email, int $bidNumber, string $showBidUrl,
                                              int $procedureFullNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Заявка отклонена";
        $message->content = "Вы отклонинили заявку №{$bidNumber}.
                             Процедура <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a>.";

        return $message;
    }

    public static function participantRejectBid(Email $email, int $bidNumber, string $showBidUrl,
                                                int $procedureFullNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Заявка отклонена";
        $message->content = "Ваша заявка <a href=\"{$showBidUrl}\">№{$bidNumber}</a> на участие в процедуре 
                            <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a> отклонена";

        return $message;
    }

    public static function organizerApproveBid(Email $email, int $bidNumber, string $showBidUrl,
                                               int $procedureFullNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Заявка принята";
        $message->content = "Вы допустили заявку №{$bidNumber}.
                             Процедура <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a>.";

        return $message;
    }

    public static function participantApproveBid(Email $email, int $bidNumber, string $showBidUrl,
                                                 int $procedureFullNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Заявка принята";
        $message->content = "Ваша заявка <a href=\"{$showBidUrl}\">№{$bidNumber}</a> на участие в процедуре 
                            <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a> допущена.";

        return $message;
    }
}