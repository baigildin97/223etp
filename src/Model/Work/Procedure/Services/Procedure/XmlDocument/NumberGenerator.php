<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Procedure\XmlDocument;

use App\ReadModel\Procedure\XmlDocument\XmlDocumentFetcher;

/**
 * Class NumberGenerator
 * @package App\Model\Work\Procedure\Services\Procedure\XmlDocument
 */
class NumberGenerator
{
    /**
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;

    /**
     * NumberGenerator constructor.
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     */
    public function __construct(XmlDocumentFetcher $xmlDocumentFetcher){
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
    }

    public function next(): int{
        return $this->xmlDocumentFetcher->findLastNumber() + 1;
    }
}