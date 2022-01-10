<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\XmlDocument;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class NumberType extends StringType
{
    public const NAME = 'procedure_xml_number_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof IdNumber ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new IdNumber($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}