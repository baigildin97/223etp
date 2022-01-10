<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\XmlDocument;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TypeStatementType extends StringType
{
    public const NAME = 'profile_xml_document_statement_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof TypeStatement ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? TypeStatement {
        return !empty($value) ? new TypeStatement($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}