<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Users\Contract;

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
    public $period;

    /**
     * Command constructor.
     * @param string $id_user
     */
    public function __construct(string $id_user){
        $this->id_user = $id_user;
    }
}