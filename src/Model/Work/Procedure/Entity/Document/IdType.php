<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Document;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class IdType extends StringType
{
    public const NAME = 'procedure_document_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Id {
        return !empty($value) ? new Id($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool {
        return true;
    }
}