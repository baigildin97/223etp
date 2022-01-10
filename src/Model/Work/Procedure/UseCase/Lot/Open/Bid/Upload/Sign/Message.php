<?php


namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Upload\Sign;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function signedDepositAgreementOrganizer(Email $email, int $bidNumber, string $showBidUrl,
                                                           int $procedureFullNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Договор подписан";
        $message->content = "Вы подписали договор о задатке по заявке <a href=\"{$showBidUrl}\">№{$bidNumber}</a>. 
                             Процедура <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a>>.";

        return $message;
    }
    public static function signedDepositAgreement(Email $email, int $procedureFullNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Договор подписан";
        $message->content = "Организатор подписал договор о задатке. Процедура
                             <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a>.";

        return $message;

    }
}