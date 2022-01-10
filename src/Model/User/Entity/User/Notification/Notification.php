<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User\Notification;

use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\User\User;
use App\Services\Notification\EmailMessageInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Notification
 * @package App\Model\User\Entity\User\Notification
 * @ORM\Entity()
 * @ORM\Table(name="notifications")
 */
class Notification
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="notification_id")
     */
    private $id;

    /**
     * @var Content
     * @ORM\Embedded(class="Content")
     */
    private $content;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * Notification constructor.
     * @param Id $id
     * @param EmailMessageInterface $message
     * @param \DateTimeImmutable $createdAt
     * @param User $user
     */
    public function __construct(Id $id,
                                 EmailMessageInterface $message,
                                 \DateTimeImmutable $createdAt,
                                User $user){
        $this->id = $id;
        $this->content = new Content($message->getSubject(), $message->getContent());
        $this->createdAt = $createdAt;
        $this->user = $user;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function read(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return Content
     */
    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

}