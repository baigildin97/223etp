<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid\Document;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class StatusType extends StringType
{
    public const NAME = 'bid_document_status';

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