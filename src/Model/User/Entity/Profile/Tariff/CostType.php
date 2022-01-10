<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Tariff;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class CostType extends StringType
{
    public const NAME = 'tariff_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Cost ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new Cost($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}