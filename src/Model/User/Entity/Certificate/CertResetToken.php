<?php
declare(strict_types=1);

namespace App\Model\User\Entity\Certificate;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ConfirmToken
 * @package App\Model\User\Entity\Certificate
 * @ORM\Embeddable()
 */
class CertResetToken
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

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $date >= $this->expires;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function timeTillExpiration(\DateTimeImmutable $date): \DateInterval
    {
        return $this->expires->diff($date, true);
    }
}