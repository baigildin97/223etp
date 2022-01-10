<?php
declare(strict_types=1);

namespace App\Container\Model\User\Service;


use App\Model\Work\Procedure\Entity\Protocol\Type;
use App\Model\Work\Procedure\Services\Procedure\Protocols\CancellationProtocolResultXmlGenerator;
use App\Model\Work\Procedure\Services\Procedure\Protocols\ProtocolGeneratorInterface;
use App\Model\Work\Procedure\Services\Procedure\Protocols\ResultsXmlGenerator;
use App\Model\Work\Procedure\Services\Procedure\Protocols\ReviewBidsXmlGenerator;
use App\Model\Work\Procedure\Services\Procedure\Protocols\WinningXmlGenerator;

/**
 * Class ProtocolGeneratorFactory
 * @package App\Container\Model\User\Service
 */
class ProtocolGeneratorFactory
{
    /**
     * @var ReviewBidsXmlGenerator
     */
    private $reviewBidsXmlGenerator;

    /**
     * @var WinningXmlGenerator
     */
    private $winningXmlGenerator;

    /**
     * @var ResultsXmlGenerator
     */
    private $resultsXmlGenerator;

    /**
     * @var CancellationProtocolResultXmlGenerator
     */
    private $cancellationProtocolResultXmlGenerator;

    /**
     * ProtocolGeneratorFactory constructor.
     * @param ReviewBidsXmlGenerator $reviewBidsXmlGenerator
     * @param WinningXmlGenerator $winningXmlGenerator
     * @param ResultsXmlGenerator $resultsXmlGenerator
     * @param CancellationProtocolResultXmlGenerator $cancellationProtocolResultXmlGenerator
     */
    public function __construct(ReviewBidsXmlGenerator $reviewBidsXmlGenerator,
                                WinningXmlGenerator $winningXmlGenerator,
                                ResultsXmlGenerator $resultsXmlGenerator,
                                CancellationProtocolResultXmlGenerator $cancellationProtocolResultXmlGenerator
    )
    {
        $this->reviewBidsXmlGenerator = $reviewBidsXmlGenerator;
        $this->winningXmlGenerator = $winningXmlGenerator;
        $this->resultsXmlGenerator = $resultsXmlGenerator;
        $this->cancellationProtocolResultXmlGenerator = $cancellationProtocolResultXmlGenerator;
    }

    /**
     * @param Type $type
     * @return ProtocolGeneratorInterface
     */
    public function get(Type $type): ProtocolGeneratorInterface
    {
        switch ($type->getName()) {
            case Type::summarizingResultsReceivingBids()->getName();
                return $this->reviewBidsXmlGenerator;
                break;
            case Type::winnerProtocol()->getName();
                return $this->winningXmlGenerator;
                break;
            case Type::resultProtocol()->getName();
                return $this->resultsXmlGenerator;
                break;
            case Type::cancellationProtocolResult()->getName();
                return $this->cancellationProtocolResultXmlGenerator;
                break;

        }
    }
}