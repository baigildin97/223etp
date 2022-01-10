<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\XmlDocument;


use App\Model\Work\Procedure\Entity\XmlDocument\IdNumber;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class IdNumberType extends StringType
{
    public const NAME = 'profile_xml_number_type';

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