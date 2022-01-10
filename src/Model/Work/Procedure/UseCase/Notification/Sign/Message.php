<?php


namespace App\Model\Work\Procedure\UseCase\Notification\Sign;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    /**
     * @param Email $email
     * @param string $siteName
     * @param int $procedureNumber
     * @return static
     */
    public static function procedurePublished(Email $email, string $siteName, int $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Процедура опубликована";
        $message->content = "Ваша процедура <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a> на электронной торговой площадке {$siteName}, опубликована";

        return $message;
    }
}