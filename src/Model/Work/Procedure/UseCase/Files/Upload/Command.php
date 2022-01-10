<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Files\Upload;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $procedureId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $fileTitle;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $fileType;

    /**
     * @var File
     * @Assert\NotBlank()
     */
    public $file;



    public function __construct(string $procedureId, string $fileType){
        $this->procedureId = $procedureId;
        $this->fileType = $fileType;
    }
}