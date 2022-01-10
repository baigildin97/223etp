<?php


namespace App\Model\User\UseCase\Certificate\Reset\Request;

use App\Container\Model\Certificate\CertificateService;
use Symfony\Component\Validator\Constraints as Assert;


class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $signedData;

    public $data;

    public function __construct(string $hash)
    {
        $this->data = $hash;
    }
}