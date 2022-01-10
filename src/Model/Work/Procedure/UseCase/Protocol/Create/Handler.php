<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Protocol\Create;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\Protocol\Id;
use App\Model\Work\Procedure\Entity\Protocol\IdNumber;
use App\Model\Work\Procedure\Entity\Protocol\Protocol;
use App\Model\Work\Procedure\Entity\Protocol\ProtocolRepository;
use App\Model\Work\Procedure\Entity\Protocol\Type;

class Handler
{
    /**
     * @var Flusher
     */
    public $flusher;

    /**
     * @var ProtocolRepository
     */
    public $protocolRepository;

    /**
     * @var ProcedureRepository
     */
    private $procedureRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param ProtocolRepository $protocolRepository
     * @param ProcedureRepository $procedureRepository
     */
    public function __construct(Flusher $flusher, ProtocolRepository $protocolRepository, ProcedureRepository $procedureRepository) {
        $this->flusher = $flusher;
        $this->protocolRepository = $protocolRepository;
        $this->procedureRepository = $procedureRepository;
    }

    public function handle(Command $command): void {
        $procedure = $this->procedureRepository->get(new \App\Model\Work\Procedure\Entity\Id($command->procedureId));

        $protocol = new Protocol(Id::next(), IdNumber::next(),Type::summarizingResultsReceivingBids(), $procedure);
    }

}