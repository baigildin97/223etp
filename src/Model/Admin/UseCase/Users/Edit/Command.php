<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Users\Edit;


use App\Model\User\Entity\User\Role;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $id_user;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $role;

    public function __construct(string $id_user){
        $this->id_user = $id_user;
    }
}