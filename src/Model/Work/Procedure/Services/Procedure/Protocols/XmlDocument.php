<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Procedure\Protocols;


class XmlDocument
{
    public $content;
    public $hash;
    public $nextStatus;

    public function __construct(string $content, string $hash, ? string $nextStatus) {
        $this->content = $content;
        $this->hash = $hash;
        $this->nextStatus = $nextStatus;
    }
}