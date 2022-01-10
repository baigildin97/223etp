<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class ContractType extends StringType
{
    public const NAME = 'procedure_contract_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Contract ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Contract {
        return !empty($value) ? new Contract($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}