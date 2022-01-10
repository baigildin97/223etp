<?php
declare(strict_types=1);
namespace App\Model\Admin\Entity\News;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class News
 * @package App\Model\Admin\Entity\News
 * @ORM\Entity()
 * @ORM\Table(name="news")
 */
class News
{

    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="news_id")
     */
    public $id;

    /**
     * @var Status
     * @ORM\Column(type="news_status")
     */
    public $status;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    public $subject;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    public $text;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="delayed_publication", nullable=true)
     */
    public $delayedPublication;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at", nullable=false)
     */
    public $createdAt;

    /**
     * News constructor.
     * @param Id $id
     * @param Status $status
     * @param string $subject
     * @param string $text
     * @param \DateTimeImmutable|null $delayedPublication
     * @param \DateTimeImmutable $createdAt
     */
    public function __construct(Id $id, Status $status, string $subject, string $text,
                                ?\DateTimeImmutable $delayedPublication,
                                \DateTimeImmutable $createdAt){
        $this->id = $id;
        $this->status = $status;
        $this->subject = $subject;
        $this->text = $text;
        $this->delayedPublication = $delayedPublication;
        $this->createdAt = $createdAt;
    }

    /**
     * @param string $subject
     * @param string $text
     */
    public function edit(string $subject, string $text){
        $this->subject = $subject;
        $this->text = $text;
    }

    public function archived(): void{
        $this->status = Status::archived();
    }
}