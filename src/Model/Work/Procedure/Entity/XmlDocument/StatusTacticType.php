<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\XmlDocument;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class StatusTacticType extends StringType
{
    public const NAME = 'procedure_xml_document_status_tactic';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof StatusTactic ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? StatusTactic{
        return !empty($value) ? new StatusTactic($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}