<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot;


use Webmozart\Assert\Assert;

class ArrestedPropertyType
{
    public const IMMOVABLE_PROPERTY = "IMMOVABLE_PROPERTY";
    public const MOVABLE_PROPERTY = "MOVABLE_PROPERTY";
    public const PLEDGED_REAL_ESTATE = "PLEDGED_REAL_ESTATE";
    public const COLLATERALIZED_MOVABLE_PROPERTY = "COLLATERALIZED_MOVABLE_PROPERTY";

    public static $values = [
        self::IMMOVABLE_PROPERTY => "Недвижимое имущество",
        self::MOVABLE_PROPERTY => "Движимое имущество",
        self::PLEDGED_REAL_ESTATE => "Залоговое недвижимое имущество",
        self::COLLATERALIZED_MOVABLE_PROPERTY => "Залоговое движимое имущество"
    ];

    private $name;

    /**
     * ArrestedPropertyType constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::IMMOVABLE_PROPERTY,
            self::MOVABLE_PROPERTY,
            self::PLEDGED_REAL_ESTATE,
            self::COLLATERALIZED_MOVABLE_PROPERTY
        ]);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isPledgedRealEstate(): bool{
        return $this->name === self::PLEDGED_REAL_ESTATE;
    }

    /**
     * @return bool
     */
    public function isCollateralizedMovableProperty(): bool{
        return $this->name === self::COLLATERALIZED_MOVABLE_PROPERTY;
    }

    public function getLocalizedName(): string {
        return self::$values[$this->name];
    }

}
