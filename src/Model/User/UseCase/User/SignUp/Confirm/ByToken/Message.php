<?php


namespace App\Model\User\UseCase\User\SignUp\Confirm\ByToken;


use App\Model\User\Entity\User\Email;
use App\Services\Notification\EmailMessageBase;

class Message extends EmailMessageBase
{
    public function __construct(Email $email, string $siteName)
    {
        $this->mailTo = $email;
        $this->subject = "Первый этап регистрации на электронной торговой площадке {$siteName}";
        $this->content = "Вы прошли первый этап регистрации на ЭТП {$siteName} - 
             создание учетной записи пользователя. Для подключения полного функционала системы,
             необходимо пройти следующий этап - заполнение и создание профиля пользователя. 
              ";
    }
}