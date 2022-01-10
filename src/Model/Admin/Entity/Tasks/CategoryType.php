<?php
declare(strict_types=1);
namespace App\Model\Admin\Entity\Tasks;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class CategoryType extends StringType
{
    public const NAME = 'tasks_category_type';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof Category ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? Category{
        return !empty($value) ? new Category($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }
}