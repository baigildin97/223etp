<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Payment\Transaction;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TransactionTypeType extends StringType
{
    public const NAME = 'transaction_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof TransactionType ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? TransactionType {
        return !empty($value) ? new TransactionType($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}