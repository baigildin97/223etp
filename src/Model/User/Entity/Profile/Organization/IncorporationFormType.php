<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Organization;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class IncorporationFormType extends StringType
{

    public const NAME = 'profile_organization_incorporation_form';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value instanceof IncorporationForm ? $value->getName(): $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ? IncorporationForm {
        return !empty($value) ? new IncorporationForm($value) : null;
    }

    public function getName(): string {
        return self::NAME;
    }

}