<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Document\Upload;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $userId;

    /**
     * @var File
     * @Assert\NotBlank()
     */
    public $file;

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


    public function __construct(string $userId, string $fileType) {
        $this->userId = $userId;
        $this->fileType = $fileType;
    }
}