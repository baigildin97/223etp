<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Payment\Transaction\Document;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class IdType extends GuidType
{
    public const NAME = 'transaction_documents_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new Id($value) : null;
    }
}