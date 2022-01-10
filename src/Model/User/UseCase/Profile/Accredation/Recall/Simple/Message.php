<?php


namespace App\Model\User\UseCase\Profile\Accredation\Recall\Simple;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function profileXmlDocumentRegistrationRecallUser(Email $email, int $docNumber,
                                                                    \DateTimeImmutable $createdAt): self
    {
        $message = new self($email);
        $message->subject = "Заявление отозвано";
        $message->content = "Ваше заявление на регистрацию (входящий №{$docNumber}) от {$createdAt->format('d.m.Y')} отозвано.";

        return $message;
    }

    public static function profileXmlDocumentRegistrationRecallModerator(
        Email $email,
        string $fullName,
        int $docNumber,
        \DateTimeImmutable $createdAt
    ): self
    {
        $message = new self($email);
        $message->subject = "Заявление отозвано";
        $message->content = "Пользователь {$fullName} отозвал заявление на регистрацию (входящий №{$docNumber}) от {$createdAt->format('d.m.Y')}.";

        return $message;
    }

    public static function profileXmlDocumentEditRecallUser(Email $email, int $docNumber,
                                                                    \DateTimeImmutable $createdAt): self
    {
        $message = new self($email);
        $message->subject = "Заявление отозвано";
        $message->content = "Ваше заявление на изменение данных пользователя (входящий №{$docNumber}) от {$createdAt->format('d.m.Y')} отозвано.";

        return $message;
    }

    public static function profileXmlDocumentEditRecallModerator(
        Email $email,
        string $fullName,
        int $docNumber,
        \DateTimeImmutable $createdAt
    ): self
    {
        $message = new self($email);
        $message->subject = "Заявление отозвано";
        $message->content = "Пользователь {$fullName} отозвал заявление на изменение данных пользователя (входящий №{$docNumber}) от {$createdAt->format('d.m.Y')}.";

        return $message;
    }

    public static function profileXmlDocumentReplacingEp(Email $email, int $docNumber,
                                                         \DateTimeImmutable $createdAt): self
    {
        $message = new self($email);
        $message->subject = "Заявление отозвано";
        $message->content = "Ваше заявление на замену электронной подписи данных пользователя (входящий №{$docNumber}) от {$createdAt->format('d.m.Y')} отозвано.";

        return $message;
    }

    public static function profileXmlDocumentReplacingEpModerator(
        Email $email,
        string $fullName,
        int $docNumber,
        \DateTimeImmutable $createdAt): self
    {
        $message = new self($email);
        $message->subject = "Заявление отозвано";
        $message->content = "Пользователь {$fullName} отозвал заявление на замену электронной подписи данных пользователя (входящий №{$docNumber}) от {$createdAt->format('d.m.Y')}.";

        return $message;
    }
}