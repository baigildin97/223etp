<?php


namespace App\Model\User\UseCase\Certificate\Reset;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function requestChangeCertificate(Email $email, string $urlConfirm, string $siteName = 'РесТорг'): self
    {
        $message = new self($email);
        $message->subject = "Замена ЭП";
        $message->content = "Вами был отправлен запрос на смену ЭЦП на площадке \"{$siteName}\". Для подтверждения запроса перейдите по 
                            <a href=\"{$urlConfirm}\">ссылке.</a>";

        return $message;
    }

    public static function confirmChangeCertificate(Email $email, string $siteName = 'РесТорг'): self
    {
        $message = new self($email);
        $message->subject = "Замена ЭП";
        $message->content = "Замена сертификата электронной подписи Вами произведена. Проверка Оператором ЭТП и активация личного кабинета будет произведена в срок не более 5 рабочих дней.";
        return $message;
    }
}