<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Role\Copy;

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
     */
    public $name;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $roleConstant;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}