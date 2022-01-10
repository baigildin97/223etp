<?php


namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Sign;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function createBid(Email $email, string $procedureNumber, string $showProcedureUrl, int $bidNumber, string $showBidUrl): self
    {
        $message = new self($email);
        $message->subject = "Вы подали заявку на участие";
        $message->content = "Вы подали заявку на участие в процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>.
                             Вашей заявке присвоен номер: <a href=\"{$showBidUrl}\">№$bidNumber</a>";

        return $message;
    }

    public static function blockedFunds(Email $email,
                                        string $procedureFullNumber,
                                        string $showProcedureUrl,
                                        int $bidNumber,
                                        string $showBidUrl,
                                        float $levelPercent,
                                        string $initialLotPrice,
                                        string $typeNds): self
    {
        $message = new self($email);
        $message->subject = "Средства гарантийного обеспечения заблокированы";
        $message->content = "Средства гарантийного обеспечения для участия в торгах <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a> по заявке
                             <a href=\"{$showBidUrl}\">№{$bidNumber}</a>, в размере {$levelPercent}% от начальной (минимальной) цены имущества,
                             в сумме {$initialLotPrice} ({$typeNds}), заблокированы на вашем виртуальном счете";

        return $message;
    }

    public static function newBidOrganizer(Email $email, string $procedureNumber, string $showProcedureUrl, int $bidNumber, string $showBidUrl): self
    {
        $message = new self($email);
        $message->subject = "Подана заявка на участие в торгах";
        $message->content = "Претендентом подана заявка №{$bidNumber} на участие в торгах <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>";

        return $message;
    }
}