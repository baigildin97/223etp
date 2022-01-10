<?php


namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Recall;


use App\Model\User\Entity\User\Email;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function BidRecall(Email $email, string $showBidUrl, int $bidNumber, int $procedureFullNumber,
                                     string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Заявка отозвана";
        $message->content = "Заявка (входящий <a href=\"{$showBidUrl}\" target=\"_blank\">№{$bidNumber}</a>) на участие в процедуре <a href=\"{
        $showProcedureUrl}\" target=\"_blank\">№{$procedureFullNumber}</a> отозвана.";
        return $message;
    }

    public static function BidRecallOrganizer(Email $email, int $bidNumber,
                                              string $showBidUrl, int $procedureFullNumber,
                                              string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Заявка отозвана";
        $message->content = "Отозвана заявка <a href=\"{$showBidUrl}\" target=\"_blank\">№{$bidNumber}</a> по вашей 
                                процедуре <a href=\"$showProcedureUrl\">№{$procedureFullNumber}</a>";

        return $message;
    }

    public static function unBlockedFunds(Email $email,
                                        string $procedureFullNumber,
                                        string $showProcedureUrl,
                                        int $bidNumber,
                                        string $showBidUrl,
                                        float $levelPercent,
                                        string $initialLotPrice,
                                        string $typeNds): self
    {
        $message = new self($email);
        $message->subject = "Средства гарантийного обеспечения разблокированы";
        $message->content = "Средства гарантийного обеспечения для участия в торгах <a href=\"{$showProcedureUrl}\">№{$procedureFullNumber}</a> по заявке
                             <a href=\"{$showBidUrl}\">№{$bidNumber}</a>, в размере {$levelPercent}% от начальной (минимальной) цены имущества,
                             в сумме {$initialLotPrice} ({$typeNds}), разблокированы на вашем виртуальном счете";

        return $message;
    }
}