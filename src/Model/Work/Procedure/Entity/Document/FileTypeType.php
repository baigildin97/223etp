<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Document;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class FileTypeType extends StringType
{
    public const NAME = 'procedure_document_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof FileType ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?FileType
    {
        return !empty($value) ? new FileType($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}