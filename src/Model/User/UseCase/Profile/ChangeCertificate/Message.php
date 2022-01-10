<?php
namespace App\Model\User\UseCase\Profile\ChangeCertificate;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    /**
     * @param Email $email
     * @param string $fullName
     * @return static
     */
    public static function resetCertificateModerator(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Замена ЭП";
        $message->content = "Пользователь: {$fullName}, отправил запрос на замену ЭП";
        return $message;
    }

    /**
     * @param Email $email
     * @return static
     */
    public static function resetCertificateUser(Email $email): self
    {
        $message = new self($email);
        $message->subject = "Замена ЭП";
        $message->content = "Замена сертификата электронной подписи Вами произведена. Проверка Оператором ЭТП и активация личного кабинета будет произведена в срок не более 5 рабочих дней.";
        return $message;
    }
}