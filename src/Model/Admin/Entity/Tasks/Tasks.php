<?php
declare(strict_types=1);

namespace App\Model\Admin\Entity\Tasks;

use App\Model\User\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tasks
 * @package App\Model\Admin\Entity\Tasks
 * @ORM\Entity()
 * @ORM\Table(name="tasks")
 */
class Tasks
{

    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="tasks_id")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @var Category
     * @ORM\Column(type="tasks_category_type")
     */
    private $status;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at", nullable=false)
     */
    private $createdAt;

}