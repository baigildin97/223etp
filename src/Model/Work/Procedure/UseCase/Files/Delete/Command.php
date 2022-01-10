<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Files\Delete;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $fileId;

    public function __construct(string $fileId) {
        $this->fileId = $fileId;
    }

}