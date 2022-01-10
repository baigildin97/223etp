<?php


namespace App\Model\User\UseCase\Profile\Accredation\Moderator\Action;


use App\Model\User\Entity\User\Email;
use App\Services\Notification\EmailMessageBase;

class Message extends EmailMessageBase
{
    public static function profileRejectUser(
        Email $email, string $siteName, int $bidId, string $cause, \DateTimeImmutable $createdAt): self
    {
        $message = new self($email);
        $message->subject = "Ваше заявление на регистрацию на ЭТП {$siteName} отклонено";
        $message->content = "Ваше заявление (входящий №{$bidId}) от " . $createdAt->format('d.m.Y') . " на регистрацию на ЭТП {$siteName} отклонено. Причина: {$cause}.
             Вы можете повторно подать заявление на регистрацию, после устранения указанных оснований для отказа.";

        return $message;
    }

    public static function profileRejectModerator(Email $email, string $fullName, string $cause): self
    {
        $message = new self($email);
        $message->subject = "Профиль полбзователя отклонен";
        $message->content = "Мы отклонили профиль пользователя: {$fullName} Причина: {$cause}";

        return $message;
    }

    public static function profileEditRejectUser(Email $email, string $siteName, string $cause): self
    {
        $message = new self($email);
        $message->subject = "Заявление отклонено";
        $message->content = "Заявление на изменение данных профиля пользователя отклонено. Причина: {$cause}.
         Вы можете повторно подать заявление на регистрацию, после устранения указанных оснований для отказа.";

        return $message;
    }

    public static function profileEditRejectModerator(Email $email, string $fullName, string $cause): self
    {
        $message = new self($email);
        $message->subject = "Заявление отклонено";
        $message->content = "Мы отклонили заявку на редактироване пользователя: {$fullName} Причина: {$cause}";

        return $message;
    }

    public static function profileSuccessRegisterOrganization(Email $email, string $siteName, string $orgName): self
    {
        $message = new self($email);
        $message->subject = "Регистрация пройдена";
        $message->content = "Вы зарегистрированы на электронной торговой площадке {$siteName} как Организатор торгов.
             Для полного доступа к функционалу системы необходимо заполнить банковские реквизиты в разделе
              «Банковские реквизиты», а также подписать договор с Оператором {$orgName}.";

        return $message;
    }

    public static function profileSuccessRegister(Email $email, string $siteName): self
    {
        $message = new self($email);
        $message->subject = "Регистрация пройдена";
        $message->content = "Вы зарегистрированы на электронной торговой площадке {$siteName}.
             Для полного доступа к функционалу системы необходимо заполнить банковские реквизиты в разделе
              «Банковские реквизиты».";

        return $message;
    }

    public static function profileSuccessRegisterModerator(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Профиль одобрен";
        $message->content = "Профиль пользователя {$fullName} одобрен";

        return $message;
    }

    public static function profileSuccessEdit(Email $email, string $siteName): self
    {
        $message = new self($email);
        $message->subject = "Профиль одобрен";
        $message->content = "Данные профиля пользователя на площадке {$siteName} успешно изменены.
         Активация личного кабинета произведена.";

        return $message;
    }

    public static function profileSuccessEditModerator(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Профиль одобрен";
        $message->content = "Заявление на редактирование данных пользователя {$fullName} одобрено.";

        return $message;
    }

    public static function profileReplaceEpModerator(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Заявление одобрено";
        $message->content = "Заявление на смену электронной подписи пользователя {$fullName} одобрено.";

        return $message;
    }
}