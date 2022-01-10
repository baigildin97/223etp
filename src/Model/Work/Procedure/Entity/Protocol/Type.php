<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Protocol;


use Webmozart\Assert\Assert;

class Type
{
    public const TYPE_SUMMARIZING_RESULTS_RECEIVING_BIDS = 'TYPE_SUMMARIZING_RESULTS_RECEIVING_BIDS';
    public const WINNER_PROTOCOL = 'WINNER_PROTOCOL';
    public const RESULT_PROTOCOL = 'RESULT_PROTOCOL';
    public const CANCELLATION_PROTOCOL_RESULT = 'CANCELLATION_PROTOCOL_RESULT';

    private $name;

    public static $names = [
        self::TYPE_SUMMARIZING_RESULTS_RECEIVING_BIDS => 'Протокол о подведении итогов приема и регистрации заявок',
        self::WINNER_PROTOCOL => 'Протокол заседания комиссии об определении победителя торгов в электронной форме',
        self::RESULT_PROTOCOL => 'Протокол о результатах торгов',
        self::CANCELLATION_PROTOCOL_RESULT => 'Протокол об аннулировании результатах торгов'
    ];

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::TYPE_SUMMARIZING_RESULTS_RECEIVING_BIDS,
            self::WINNER_PROTOCOL,
            self::RESULT_PROTOCOL,
            self::CANCELLATION_PROTOCOL_RESULT
        ]);
        $this->name = $name;
    }

    public static function summarizingResultsReceivingBids(): self {
        return new self(self::TYPE_SUMMARIZING_RESULTS_RECEIVING_BIDS);
    }

    public static function resultProtocol(): self{
        return new self(self::RESULT_PROTOCOL);
    }

    public static function winnerProtocol(): self {
        return new self(self::WINNER_PROTOCOL);
    }

    public static function cancellationProtocolResult(): self {
        return new self(self::CANCELLATION_PROTOCOL_RESULT);
    }

    public function isSummarizingResultsReceivingBids(): bool {
        return $this->name === self::TYPE_SUMMARIZING_RESULTS_RECEIVING_BIDS;
    }

    public function isResultProtocol(): bool{
        return $this->name === self::RESULT_PROTOCOL;
    }

    public function isWinnerProtocol(): bool {
        return $this->name === self::WINNER_PROTOCOL;
    }

    public function isCancellationProtocolResult(): bool {
        return $this->name === self::CANCELLATION_PROTOCOL_RESULT;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLocalizedName(): string{
        return self::$names[$this->name];
    }
}