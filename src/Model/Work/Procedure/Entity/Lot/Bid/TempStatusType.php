<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TempStatusType extends StringType
{
    public const NAME = 'bid_temp_status';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof TempStatus ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? TempStatus {
        return !empty($value) ? new TempStatus($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}