<?php


namespace App\Model\Work\Procedure\UseCase\Protocol\Sign\Organizer;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{

    public static function payOperatorServices(Email $email, string $procedureNumber, string $initialLotPrice, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Списание средств";
        $message->content = "Списание средств в счет оплаты услуг Оператора электронной площадки победителем
         по торговой процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a> в сумме {$initialLotPrice} осуществлено";

        return $message;
    }

    public static function publishedProtocolWinner(Email $email, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Протокол опубликован для подписи";
        $message->content = "Организатор опубликовал протокол о результатах торгов, вам необходимо подписать
         протокол по торговой процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>";

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

    public static function organizerPublishedProtocolResults(Email $email, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Протокол опубликован";
        $message->content = "Организатор опубликовал протокол об результатах торгов по торговой процедуре
                             <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>.";

        return $message;
    }

    public static function participantPublishedProtocolWinner(Email $email, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Протокол опубликован";
        $message->content = "Организатор опубликовал протокол об определении победителя торгов по торговой 
                             процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>.";

        return $message;
    }

    public static function setPlaceParticipant(Email $email,  int $place, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Вашей заявке присвоено место";
        $message->content = "Вашей заявке присвоено место №{$place} по 
                             процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>.";

        return $message;
    }

    public static function organizerPublishedProtocolWinner(Email $email, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Протокол опубликован";
        $message->content = "Вы опубликовали протокол об определении победителя торгов по торговой 
                             процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>.";

        return $message;
    }

    public static function organizerPublishedProtocol(Email $email, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Протокол опубликован";
        $message->content = "Вы опубликовали протокол о результатах торгов по торговой
                             процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>.";

        return $message;
    }

    public static function participantPublishedProtocolSummingRegProcedure(Email $email, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Протокол опубликован";
        $message->content = "Организатор опубликовал протокол о подведении итогов приема и регистрации заявок по
                             торговой процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>";

        return $message;
    }


    public static function organizerPublishedProtocolSummingRegProcedure(Email $email, string $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Протокол опубликован";
        $message->content = "Вы опубликовали протокол о подведении итогов приема и регистрации заявок по
                             торговой процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>";

        return $message;
    }


/*    public static function participantPublishedProtocolWinner(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Организатор опубликовал протокол о результатах торгов, вам необходимо подписать протокол по торговой процедуре №{$procedureFullNumber}",
            "text" => "Организатор опубликовал протокол о результатах торгов, вам необходимо подписать протокол по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }*/
}