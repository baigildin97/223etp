<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Action\Open\Edit;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\ProcedureRepository;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var ProcedureRepository
     */
    private $procedureRepository;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param ProcedureRepository $procedureRepository
     * @param ProfileRepository $profileRepository
     */
    public function __construct(Flusher $flusher, ProcedureRepository $procedureRepository, ProfileRepository $profileRepository)
    {
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void{
        $procedure = $this->procedureRepository->get(new Id($command->procedure_id));
        $profileOrganizer = $this->profileRepository->getByUser(new \App\Model\User\Entity\User\Id($command->userId));
        if ($profileOrganizer->getId()->getValue() !== $procedure->getOrganizer()->getId()->getValue()){
            throw new \DomainException('Access Denied');
        }

        $procedure->edit(
            $command->procedureName,
            $command->infoPointEntry,
            $command->infoTradingVenue,
            $command->infoBiddingProcess
        );

        $this->flusher->flush();
    }

}