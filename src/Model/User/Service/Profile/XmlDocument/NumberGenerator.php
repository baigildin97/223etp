<?php
declare(strict_types=1);
namespace App\Model\User\Service\Profile\XmlDocument;


use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;

class NumberGenerator
{
    private $xmlDocumentFetcher;

    public function __construct(XmlDocumentFetcher $xmlDocumentFetcher){
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
    }

    public function next(): int{
        return $this->xmlDocumentFetcher->findLastIdNumber() + 1;
    }
}