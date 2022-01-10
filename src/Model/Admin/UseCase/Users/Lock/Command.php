<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Users\Lock;


class Command
{
    /**
     * @var string
     */
    public $id_user;

    /**
     * @var string
     */
    public $cause;

    /**
     * Command constructor.
     * @param string $id_user
     * @param string $cause
     */
    public function __construct(string $id_user){
        $this->id_user = $id_user;
    }
}