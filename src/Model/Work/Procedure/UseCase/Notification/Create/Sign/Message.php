<?php


namespace App\Model\Work\Procedure\UseCase\Notification\Create\Sign;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function procedureRejectOrganizer(Email $email, int $procedureNumber, string $showProcedureUrl, int $notId): self
    {
        $message = new self($email);
        $message->subject = "Отмена процедуры";
        $message->content = "Опубликовано извещение №{$notId} об отмене торговой процедуры <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>.";

        return $message;
    }


    public static function blockedFundsUnblocked(Email $email,
                                                 string $procedureIdNumber,
                                                 string $showProcedureUrl,
                                                 int $bidNumber,
                                                 string $showBidUrl,
                                                 float $levelPercent,
                                                 string $initialLotPrice,
                                                 string $typeNds): self
    {
        $message = new self($email);
        $message->subject = "Разблокировка средств";
        $message->content = "Средства гарантийного обеспечения для участия в торгах <a href=\"{$showProcedureUrl}\">№{$procedureIdNumber}</a>
         по заявке <a href=\"{$showBidUrl}\">№{$bidNumber}</a>, в размере {$levelPercent}% от начальной (минимальной) цены имущества,
          в сумме {$initialLotPrice} ({$typeNds}), разблокированы на вашем виртуальном счете";

        return $message;
    }
}