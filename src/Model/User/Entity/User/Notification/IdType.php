<?php
declare(strict_types=1);
namespace App\Model\User\Entity\User\Notification;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class IdType extends StringType
{
    public const NAME = "notification_id";

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Id ? $value->getValue() : $value;
    }


    public function convertToPHPValue($value, AbstractPlatform $platform): ? Id{
        return !empty($value) ? new Id($value): null ;
    }

    public function getName(): string {
        return self::NAME;
    }
}