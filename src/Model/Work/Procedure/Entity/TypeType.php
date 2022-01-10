<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TypeType extends StringType
{

    public const NAME = 'procedure_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Type ? $value->getValue(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Type {
        return !empty($value) ? new Type($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }

}