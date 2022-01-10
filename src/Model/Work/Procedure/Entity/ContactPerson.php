<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ContactPerson
 * @package App\Model\Procedure\Entity
 * @ORM\Embeddable()
 */
class ContactPerson
{
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $phone;

    /**
     * ContactPerson constructor.
     * @param string $name
     * @param string $email
     * @param string $phone
     */
    public function __construct(string $name, string $email, string $phone) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

}