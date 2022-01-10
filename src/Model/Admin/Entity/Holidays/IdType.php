<?php
declare(strict_types=1);
namespace App\Model\Admin\Entity\Holidays;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

/**
 * Class IdType
 * @package App\Model\Admin\Entity\Holidays
 */
class IdType extends GuidType
{
    public const NAME = 'holidays_id';

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Id|mixed|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new Id($value) : null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}