<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Document;

use App\ReadModel\Procedure\Lot\DetailView;
use Webmozart\Assert\Assert;

/**
 * Class FileType
 * @package App\Model\Work\Procedure\Entity\Document
 */
class FileType
{
    public const DEPOSIT_AGREEMENT = 'DEPOSIT_AGREEMENT';
    public const CONTRACT_OF_SALE = 'CONTRACT_OF_SALE';
    public const OTHER = 'OTHER';
    public const DOCUMENT_COMPOSITION = 'DOCUMENT_COMPOSITION';

    public static $typesNames = [
        FileType::DEPOSIT_AGREEMENT => 'Проект договора о задатке',
        FileType::CONTRACT_OF_SALE => 'Проект договора купли-продажи*',
        FileType::OTHER => 'Дополнительные документы',
        FileType::DOCUMENT_COMPOSITION => 'Документ о составе комиссии организатора'
    ];

    private $value;

    /**
     * @param string $typeName
     * @return string
     */
    public static function getTypeName(string $typeName): string
    {
        return self::$typesNames[$typeName];
    }
    
    /**
     * Status constructor.
     * @param string $value
     */
    public function __construct(string $value) {
        Assert::oneOf($value,[
            self::DEPOSIT_AGREEMENT,
            self::CONTRACT_OF_SALE,
            self::OTHER,
            self::DOCUMENT_COMPOSITION
        ]);
        $this->value = $value;
    }

    public function getLocalizedName(): string
    {
        return self::$typesNames[$this->value];
    }

    /**
     * @return string
     */
    public function getValue(): string {
        return $this->value;
    }

    /**
     * @param DetailView $detailView
     * @return string[]
     */
    public static function getFileCategories(DetailView $detailView)
    {
      return self::$typesNames;
    }

}