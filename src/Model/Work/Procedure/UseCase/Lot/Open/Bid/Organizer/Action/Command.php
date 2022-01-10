<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Action;

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
     */
    public $cause;

    public function __construct(string $bidId){
        $this->bidId = $bidId;
    }
}