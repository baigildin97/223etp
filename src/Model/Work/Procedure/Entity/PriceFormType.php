<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class PriceFormType extends StringType
{
    public const NAME = 'procedure_price_form';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof PriceForm ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? PriceForm{
        return !empty($value) ? new PriceForm($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}