<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\SignUp\Request;


use App\Model\User\Entity\User\Email;
use App\Services\Notification\EmailMessageBase;

class Message extends EmailMessageBase
{
    public static function confirmSignUp(Email $email, string $siteName, string $urlConfirm): self
    {
        $message = new self($email);
        $message->subject = 'Подтверждение регистрации';
        $message->content = "Ваш адрес электронной почты был использован для регистрации нового пользователя на ЭТП {$siteName}.
        Для подтверждения запроса на регистрацию перейдите по <a href=\"$urlConfirm\">ссылке.</a>";
        return $message;
    }
}