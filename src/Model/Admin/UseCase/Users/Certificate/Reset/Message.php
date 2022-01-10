<?php
namespace App\Model\Admin\UseCase\Users\Certificate\Reset;


use App\Model\User\Entity\User\Email;
use App\Services\Notification\EmailMessageBase;

class Message extends EmailMessageBase
{
    public static function certificateRejectUser(Email $email, string $cause): self {
        $message = new self($email);
        $message->subject = "Замена сертификата ЭП";
        $message->content = "Активация личного кабинета не произведена. Регистрация на ЭТП \"РесТорг\" не подтверждена. Причина: {$cause}.";
        return $message;
    }


    public static function certificateRejectModerator(Email $email, string $cause): self {
        $message = new self($email);
        $message->subject = "Замена сертификата ЭП";
        $message->content = "Активация личного кабинета не произведена. Регистрация на ЭТП \"РесТорг\" не подтверждена. Причина: {$cause}.";

        return $message;
    }

    /**
     * @param Email $email
     * @return static
     */
    public static function certificateSuccessUser(Email $email): self {
        $message = new self($email);
        $message->subject = "Замена сертификата ЭП";
        $message->content = "Активация личного кабинета произведена. Регистрация на ЭТП \"РесТорг\" подтверждена.";
        return $message;
    }

    /**
     * @param Email $email
     * @param string $userFullName
     * @return static
     */
    public static function certificateSuccessModerator(Email $email, string $userFullName): self
    {
        $message = new self($email);
        $message->subject = "Замена сертификата ЭП";
        $message->content = "Подтвердили замену ЭП. Пользователю, {$userFullName}";

        return $message;
    }
}