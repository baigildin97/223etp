<?php
declare(strict_types=1);
namespace App\Model\User\Entity\User\Notification;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Content
 * @package App\Model\User\Entity\User\Notification
 * @ORM\Embeddable()
 */
class Content
{
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $subject;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * Content constructor.
     * @param string $subject
     * @param string $text
     */
    public function __construct(string $subject, string $text)
    {
        $this->subject = $subject;
        $this->text = $text;
    }
}