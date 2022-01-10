<?php


namespace App\Command\Cron\User;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function cancellationContract(Email $email, string $findNameOrganization): self
    {
        $message = new self($email);
        $message->subject = "Срок договора истек.";
        $message->content = "Договор с {$findNameOrganization} истек.";

        return $message;
    }
}