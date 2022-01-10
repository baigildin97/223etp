<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot;


use Webmozart\Assert\Assert;

class Nds
{
    private const NDS_IS_EXEMPT = "NDS_IS_EXEMPT";
    private const INCLUDING_NDS = "INCLUDING_NDS";
    private const NO_NDS = "NO_NDS";

    private static $values = [
        self::NDS_IS_EXEMPT => "НДС не облагается",
        self::INCLUDING_NDS => "В том числе НДС",
        self::NO_NDS => "Без НДС"
    ];

    private $name;

    public function __construct(string $name){
        Assert::oneOf($name,[
            self::NDS_IS_EXEMPT,
            self::INCLUDING_NDS,
            self::NO_NDS
        ]);
        $this->name = $name;
    }

    public static function ndsIsExempt(): self{
        return new self(self::NDS_IS_EXEMPT);
    }

    public static function includingNds(): self{
        return new self(self::INCLUDING_NDS);
    }

    public static function noNds(): self{
        return new self(self::NO_NDS);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    public function getLocalizedName(): string{
        return self::$values[$this->name];
    }
}
