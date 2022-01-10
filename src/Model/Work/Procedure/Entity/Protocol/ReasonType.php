<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Protocol;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class ReasonType extends StringType
{
    public const NAME = 'protocol_reason_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Reason ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Reason{
        return !empty($value) ? new Reason($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}