<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Document\Delete;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $lotId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $documentId;

    /**
     * Command constructor.
     * @param string $lotId
     * @param string $documentId
     */
    public function __construct(string $lotId, string $documentId) {
        $this->lotId = $lotId;
        $this->documentId = $documentId;
    }

}