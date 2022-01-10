<?php


namespace App\Model\Admin\UseCase\Users\Contract;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function signContract(Email $email, string $findNameOrganization, string $findSiteName): self
    {
        $message = new self($email);
        $message->subject = "Договор подписан";
        $message->content = "Договор с {$findNameOrganization} подписан. 
            Возможность размещать процедуры на ЭТП {$findSiteName}, как Организатор торгов, доступна.";

        return $message;
    }
}