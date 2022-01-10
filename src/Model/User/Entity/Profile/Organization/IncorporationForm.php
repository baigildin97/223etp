<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Organization;

use Webmozart\Assert\Assert;

class IncorporationForm
{

    private const FORM_INCORPORATION_LEGAL_ENTITY = 'LEGAL_ENTITY';
    private const FORM_INCORPORATION_INDIVIDUAL_ENTREPRENEUR = 'INDIVIDUAL_ENTREPRENEUR';
    private const FORM_INCORPORATION_INDIVIDUAL = 'INDIVIDUAL';

    private $name;

    private static $namesDescription = [
        self::FORM_INCORPORATION_INDIVIDUAL => 'Физическое лицо',
        self::FORM_INCORPORATION_INDIVIDUAL_ENTREPRENEUR => 'Индивидуальный предприниматель',
        self::FORM_INCORPORATION_LEGAL_ENTITY => 'Юридическое лицо'
    ];

    public function __construct(string $name) {
        Assert::oneOf($name,[
            self::FORM_INCORPORATION_INDIVIDUAL,
            self::FORM_INCORPORATION_INDIVIDUAL_ENTREPRENEUR,
            self::FORM_INCORPORATION_LEGAL_ENTITY
        ]);
        $this->name = $name;
    }

    public static function legalEntity(): self {
        return new self(self::FORM_INCORPORATION_LEGAL_ENTITY);
    }

    public static function individualEntrepreneur(): self {
        return new self(self::FORM_INCORPORATION_INDIVIDUAL_ENTREPRENEUR);
    }

    public static function individual(): self {
        return new self(self::FORM_INCORPORATION_INDIVIDUAL);
    }

    public function isLegalEntity(): bool {
        return $this->name === self::FORM_INCORPORATION_LEGAL_ENTITY;
    }

    public function isIndividualEntrepreneur(): bool {
        return $this->name === self::FORM_INCORPORATION_INDIVIDUAL_ENTREPRENEUR;
    }

    public function isIndividual(): bool {
        return $this->name === self::FORM_INCORPORATION_INDIVIDUAL;
    }

    public function isEqual(self $incorporationForm): bool {
        return $this->getName() === $incorporationForm->getName();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLocalizedName(): string{
        return self::$namesDescription[$this->name];
    }

}