<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User\Notification;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;


class NotificationTypeType extends TextType
{
    public const NAME = 'notification_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof NotificationType ? json_encode($value->getData(), JSON_UNESCAPED_UNICODE) : $value;


    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? NotificationType
    {
        return !empty($value) ? new NotificationType(json_decode($value, true)) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }


}