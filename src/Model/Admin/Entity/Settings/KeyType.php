<?php
declare(strict_types=1);
namespace App\Model\Admin\Entity\Settings;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class KeyType extends StringType
{
    public const NAME = 'settings_key_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Key ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Key{
        return !empty($value) ? new Key($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}