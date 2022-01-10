<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\SignUp\Request;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
//
//    /**
//     * @var string
//     * @Assert\NotBlank()
//     * @Assert\Length(min="3", max="64")
//     */
//    public $firstName;
//    /**
//     * @var string
//     * @Assert\NotBlank()
//     * @Assert\Length(min="3", max="64")
//     */
//    public $lastName;
//    /**
//     * @var string
//     * @Assert\NotBlank()
//     * @Assert\Length(min="3", max="64")
//     */
//    public $middleName;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

/*    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="11", max="11")

    public $phone;*/

    /**
     * @var string
     * @Assert\Length(min=6)
     */
    public $plainPassword;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $clientIp;

    /**
     * @var string
     */
    public $captcha;

    public function __construct(string $clientIp)
    {
        $this->clientIp = $clientIp;
    }
}