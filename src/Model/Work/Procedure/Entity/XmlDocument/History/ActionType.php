<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\XmlDocument\History;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class ActionType extends StringType
{
    public const NAME = 'procedure_xml_document_history_action_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value instanceof Action ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Action {
        return !empty($value) ? new Action($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}