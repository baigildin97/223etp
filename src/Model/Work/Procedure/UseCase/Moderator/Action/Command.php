<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Moderator\Action;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $procedureXmlDocumentId;

    /**
     * @var string
     */
    public $cause;


    public function __construct(string $procedureXmlDocumentId){
        $this->procedureXmlDocumentId = $procedureXmlDocumentId;
    }
}