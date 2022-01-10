<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\Email\Request;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $client_ip;

    /**
     * Command constructor.
     * @param string $id
     * @param string $client_ip
     */
    public function __construct(string $id, string $client_ip)
    {
        $this->id = $id;
        $this->client_ip = $client_ip;
    }
}
