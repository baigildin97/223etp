<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class ReloadType extends StringType
{
    public const NAME = 'reload_lot_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Reload ? $value->getValue(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Reload{
        return !empty($value) ? new Reload($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}