<?php
declare(strict_types=1);
namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Name
{
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $first;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $last;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $middle;

    public function __construct(string $first, string $last, string $middle)
    {
        Assert::notEmpty($first);
        Assert::notEmpty($last);
        $this->first = $first;
        $this->last = $last;
        $this->middle = $middle;
    }


    public function getFirst(): string
    {
        return $this->first;
    }
    public function getLast(): string
    {
        return $this->last;
    }

    public function getMiddle(): string {
        return $this->middle;
    }

    public function getFull(): string
    {
        return $this->last . ' ' . $this->first . ' ' . $this->middle;
    }
}