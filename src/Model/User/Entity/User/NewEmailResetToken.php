<?php
declare(strict_types=1);
namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * Class NewEmailResetToken
 * @package App\Model\User\Entity\User
 * @ORM\Embeddable()
 */
class NewEmailResetToken
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private $token;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $expires;

    public function __construct(string $token, \DateTimeImmutable $expires)
    {
        Assert::notEmpty($token);
        $this->token = $token;
        $this->expires = $expires;
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool {
        return $this->expires <= $date;
    }

    public function isEmpty(): bool {
        return empty($this->token);
    }

    public function getToken(): string {
        return $this->token;
    }
}