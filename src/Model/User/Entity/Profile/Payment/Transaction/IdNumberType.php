<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Payment\Transaction;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class IdNumberType extends StringType
{
    public const NAME = 'payment_transaction_id_number';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof IdNumber ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?IdNumber
    {
        return !empty($value) ? new IdNumber($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

}
