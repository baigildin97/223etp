<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Commission\Commission;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class StatusType extends StringType
{
    public const NAME = 'commission_status';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Status ? $value->getValue(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Status{
        return !empty($value) ? new Status($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}