<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;


use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_ACTIVE = 'STATUS_ACTIVE';
    public const STATUS_MODERATE = 'STATUS_MODERATE';
    public const STATUS_MODERATED = 'STATUS_MODERATED';
    public const STATUS_ARCHIVED = 'STATUS_ARCHIVED';
    public const STATUS_REJECTED = 'STATUS_REJECTED';
    public const STATUS_CANCEL = 'STATUS_CANCEL';
    public const STATUS_PAUSE = 'STATUS_PAUSE';
    public const STATUS_RESUME = 'STATUS_RESUME';
    public const STATUS_NEW = 'STATUS_NEW';
    public const STATUS_FAILED = 'STATUS_FAILED';
    public const STATUS_ACCEPTING_APPLICATIONS = 'STATUS_ACCEPTING_APPLICATIONS';
    public const STATUS_APPLICATIONS_RECEIVED = 'STATUS_APPLICATIONS_RECEIVED';
    public const STATUS_SUMMING_UP_APPLICATIONS = 'STATUS_SUMMING_UP_APPLICATIONS';
    public const STATUS_START_OF_TRADING = 'STATUS_START_OF_TRADING';
    public const STATUS_BIDDING_PROCESS = 'STATUS_BIDDING_PROCESS';
    public const STATUS_BIDDING_COMPLETED = 'STATUS_BIDDING_COMPLETED';
    public const STATUS_SIGNED_PROTOCOL_RESULT = 'STATUS_SIGNED_PROTOCOL_RESULT';
    public const STATUS_CANCELLATION_PROTOCOL_RESULT = 'STATUS_CANCELLATION_PROTOCOL_RESULT';


    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::STATUS_ACTIVE,
            self::STATUS_MODERATE,
            self::STATUS_MODERATED,
            self::STATUS_ARCHIVED,
            self::STATUS_REJECTED,
            self::STATUS_NEW,
            self::STATUS_ACCEPTING_APPLICATIONS,
            self::STATUS_APPLICATIONS_RECEIVED,
            self::STATUS_SUMMING_UP_APPLICATIONS,
            self::STATUS_START_OF_TRADING,
            self::STATUS_BIDDING_PROCESS,
            self::STATUS_BIDDING_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_CANCEL,
            self::STATUS_RESUME,
            self::STATUS_PAUSE,
            self::STATUS_SIGNED_PROTOCOL_RESULT,
            self::STATUS_CANCELLATION_PROTOCOL_RESULT
        ]);
        $this->name = $name;
    }

    public static function active(): self {
        return new self(self::STATUS_ACTIVE);
    }

    public static function cancel(): self{
        return new self(self::STATUS_CANCEL);
    }

    public static function pause(): self{
        return new self(self::STATUS_PAUSE);
    }

    public static function resume(): self{
        return new self(self::STATUS_RESUME);
    }

    public static function moderate(): self{
        return new self(self::STATUS_MODERATE);
    }

    public static function moderated(): self{
        return new self(self::STATUS_MODERATED);
    }

    public static function archived(): self{
        return new self(self::STATUS_ARCHIVED);
    }

    public static function failed(): self{
        return new self(self::STATUS_FAILED);
    }

    public static function rejected(): self{
        return new self(self::STATUS_REJECTED);
    }

    public static function new(): self{
        return new self(self::STATUS_NEW);
    }

    public static function acceptingApplications(): self{
        return new self(self::STATUS_ACCEPTING_APPLICATIONS);
    }

    public static function applicationsReceived(): self{
        return new self(self::STATUS_APPLICATIONS_RECEIVED);
    }

    public static function statusSummingUpApplications(): self {
        return new self(self::STATUS_SUMMING_UP_APPLICATIONS);
    }

    public static function statusStartOfTrading(): self {
        return new self(self::STATUS_START_OF_TRADING);
    }

    public static function statusBiddingProcess(): self {
        return new self(self::STATUS_BIDDING_PROCESS);
    }

    public static function statusBiddingCompleted(): self {
        return new self(self::STATUS_BIDDING_COMPLETED);
    }

    public static function statusSignedProtocolResult(): self {
        return new self(self::STATUS_SIGNED_PROTOCOL_RESULT);
    }

    public static function cancellationProtocolResult(): self {
        return new self(self::STATUS_CANCELLATION_PROTOCOL_RESULT);
    }

    public function isActive(): bool {
        return $this->name === self::STATUS_ACTIVE;
    }

    public function isModerate(): bool {
        return $this->name === self::STATUS_MODERATE;
    }

    public function isModerated(): bool{
        return $this->name === self::STATUS_MODERATED;
    }

    public function isFailed(): bool {
        return $this->name === self::STATUS_FAILED;
    }

    public function isArchive(): bool {
        return $this->name === self::STATUS_ARCHIVED;
    }

    public function isRejected(): bool {
        return $this->name === self::STATUS_REJECTED;
    }

    public function isNew(): bool {
        return $this->name === self::STATUS_NEW;
    }

    public function isAcceptingApplications(): bool {
        return $this->name === self::STATUS_ACCEPTING_APPLICATIONS;
    }

    public function isCancellationProtocolResult(): bool {
        return $this->name === self::STATUS_CANCELLATION_PROTOCOL_RESULT;
    }

    public function isSummingApplications(): bool {
        return $this->name === self::STATUS_SUMMING_UP_APPLICATIONS;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}
