<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Requisite;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class IdType extends StringType
{
    public const NAME = 'profile_requisite_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Id {
        return !empty($value) ? new Id($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool {
        return true;
    }
}