<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Tariff;


use Webmozart\Assert\Assert;

class Cost
{
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::numeric($value);
        $this->value = $value;
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