<?php
declare(strict_types=1);
namespace App\Model\Front\Entity\OldProcedure\Protocols;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Class Id
 * @package App\Model\Front\Entity\Procedure\Protocols
 */
class Id
{
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}