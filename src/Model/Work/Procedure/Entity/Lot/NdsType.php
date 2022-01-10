<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class NdsType extends StringType
{

    public const NAME = 'nds_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Nds ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Nds{
        return !empty($value) ? new Nds($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}