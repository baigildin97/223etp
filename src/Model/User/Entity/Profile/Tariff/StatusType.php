<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Tariff;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class StatusType extends StringType
{
    public const NAME = 'tariff_status_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Status ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Status{
        return !empty($value) ? new Status($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }

}