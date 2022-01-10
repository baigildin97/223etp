<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class ConductingTypeType extends StringType
{

    public const NAME = 'conducting_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof ConductingType ? $value->getValue(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? ConductingType {
        return !empty($value) ? new ConductingType($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }

}