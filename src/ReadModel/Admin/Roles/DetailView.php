<?php
declare(strict_types=1);
namespace App\ReadModel\Admin\Roles;



class DetailView
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $role_constant;

    /**
     * @var array
     */
    public $permissions;

}