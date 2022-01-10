<?php


namespace App\Model\User\UseCase\Profile\Accredation\Sign;


use App\Model\User\Entity\User\Email;

class Message extends \App\Services\Notification\EmailMessageBase
{
    public static function userSingedModerateProfileRepeat(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Запрос на модерацию";
        $message->content = "Пользователь {$fullName} ожидает повторной модерации профиля";

        return $message;
    }

    public static function userReplacingEp(Email $email, int $idBid, string $showBidUrl, string $findAccreditationPeriod,
                                           \DateTimeImmutable $createdAt): self
    {
        $message = new self($email);
        $message->subject = "Запрос на модерацию";
        $message->content = "Вами отправлено заявление на замену электронной подписи 
            (входящий <a href=\"{$showBidUrl}\">№{$idBid}</a>) от " .
            $createdAt->format('d.m.Y') . ". Вы получите уведомление о результатах рассмотрения заявки
             в течение {$findAccreditationPeriod} рабочих дней.";

        return $message;
    }

    public static function userRepeatSingedProfile(Email $email, int $idBid, string $showBidUrl, string $findAccreditationPeriod,
                                                   \DateTimeImmutable $createdAt): self
    {
        $message = new self($email);
        $message->subject = "Запрос на модерацию";
        $message->content = "Вами отправлено заявление на редактирование регистрационных данных
            (входящий <a href=\"{$showBidUrl}\">№{$idBid}</a>) от " . $createdAt->format('d.m.Y') .
            ". Вы получите уведомление о результатах рассмотрения заявки в
             течение {$findAccreditationPeriod} рабочих дней.";

        return $message;
    }

    public static function userSingedModerateProfile(Email $email, string $fullName): self
    {
        $message = new self($email);
        $message->subject = "Запрос на модерацию";
        $message->content = "Пользователь {$fullName} ожидает модерации профиля";

        return $message;
    }

    public static function userSingedProfile(Email $email, string $fullName, string $findSiteName, int $idBid,
                                             string $showBidUrl, $accreditationPeriod, $getIncorporatedForm): self
    {
        $message = new self($email);
        $message->subject = "Запрос на регистрацию";
        $message->content = "
             Уважаемый(ая) $fullName. 
             Ваше заявление на регистрацию на ЭТП {$findSiteName} поставлено в очередь на рассмотрение Администратором системы. <br/>
             Ему присвоен номер <a href=\"{$showBidUrl}\">№{$idBid}</a>. По результатам проверки данных пользователя, в случае отклонения вашего заявления, предоставляется возможность редактирования данных и повторной подачи заявления. 
             <br/>
             Уведомление о результатах проверки ожидайте в течение {$accreditationPeriod} рабочих дней.
            ";

        return $message;
    }
}
