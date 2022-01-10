<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Protocol;


use Webmozart\Assert\Assert;

class Reason
{
    private const LESS_2_BIDS = 'LESS_2_BIDS';
    private const APPROVE_LESS_2_BIDS = 'APPROVE_LESS_2_BIDS';
    private const APPROVE_MORE_2_BIDS = 'APPROVE_MORE_2_BIDS';
    private const ZERO_OFFERS = 'ZERO_OFFERS';
    private const ZERO_CONFIRMED_BIDS = 'CONFIRM_BIDS';
    private const CONFIRMED_LESS_2_BIDS = 'CONFIRM_LESS_2_BIDS';
    private const NO_REASON = 'NO_REASON';

    private static $reasons = [
        self::LESS_2_BIDS => 'Подано менее 2-х заявок',
        self::APPROVE_LESS_2_BIDS => 'Допущено менее 2-х заявок',
        self::APPROVE_MORE_2_BIDS => 'Допущено более 2-х заявок',
        self::ZERO_OFFERS => 'Никто не сделал надбавки к начальной цене имущества',
        self::ZERO_CONFIRMED_BIDS => 'Никто не принял участие в торгах',
        self::CONFIRMED_LESS_2_BIDS => 'В торгах принял участие только 1 участник',
        self::NO_REASON => ' '
    ];

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::LESS_2_BIDS,
            self::APPROVE_LESS_2_BIDS,
            self::APPROVE_MORE_2_BIDS,
            self::ZERO_OFFERS,
            self::ZERO_CONFIRMED_BIDS,
            self::CONFIRMED_LESS_2_BIDS,
            self::NO_REASON
        ]);
        $this->name = $name;
    }

    public static function lessTwoBids(): self {
        return new self(self::LESS_2_BIDS);
    }

    public static function none(): self{
        return new self(self::NO_REASON);
    }

    public static function approveLessTwoBids(): self{
        return new self(self::APPROVE_LESS_2_BIDS);
    }

    public static function approveMoreTwoBids(): self {
        return new self(self::APPROVE_MORE_2_BIDS);
    }

    public static function zeroOffers(): self {
        return new self(self::ZERO_OFFERS);
    }

    public static function zeroConfirmBids(): self {
        return new self(self::ZERO_CONFIRMED_BIDS);
    }

    public static function confirmedLessTwoBids(): self {
        return new self(self::CONFIRMED_LESS_2_BIDS);
    }

    public function isLessTwoBids(): bool {
        return $this->name === self::LESS_2_BIDS;
    }

    public function isApproveLessTwoBids(): bool {
        return $this->name === self::APPROVE_LESS_2_BIDS;
    }

    public function isApproveMoreTwoBids(): bool {
        return $this->name === self::APPROVE_MORE_2_BIDS;
    }

    public function isZeroOffers(): bool {
        return $this->name === self::ZERO_OFFERS;
    }

    public function isZeroConfirmedBids(): bool {
        return $this->name === self::ZERO_CONFIRMED_BIDS;
    }

    public function isConfirmedLessTwoBids(): bool {
        return $this->name === self::CONFIRMED_LESS_2_BIDS;
    }



    public function isEqual(self $reason): bool {
        return $this->getName() === $reason->getName();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLocalizedName(): string {
        return self::$reasons[$this->name];
    }
}