<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Delete;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bidId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $documentId;

    /**
     * Command constructor.
     * @param string $bidId
     * @param string $documentId
     */
    public function __construct(string $bidId, string $documentId) {
        $this->bidId = $bidId;
        $this->documentId = $documentId;
    }

}