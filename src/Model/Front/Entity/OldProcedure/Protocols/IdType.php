<?php
declare(strict_types=1);
namespace App\Model\Front\Entity\OldProcedure\Protocols;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class IdType extends GuidType
{
    public const NAME = 'old_protocol_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {

        return $value instanceof Id ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        //  dd($value);
        return !empty($value) ? new Id($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
}