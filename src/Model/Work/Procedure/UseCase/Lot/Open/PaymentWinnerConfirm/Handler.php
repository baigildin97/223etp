<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\PaymentWinnerConfirm;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Lot\Id;
use App\Model\Work\Procedure\Entity\Lot\LotRepository;
use App\Model\Work\Procedure\Entity\ProcedureRepository;

class Handler
{
    private $flusher;
    private $lotRepository;
    private $procedureRepository;

    public function __construct(Flusher $flusher, LotRepository $lotRepository, ProcedureRepository $procedureRepository){
        $this->flusher = $flusher;
        $this->lotRepository = $lotRepository;
        $this->procedureRepository = $procedureRepository;
    }

    public function handleConfirm(Command $command){
        $lot = $this->lotRepository->get(new Id($command->lot_id));
        $procedure = $this->procedureRepository->get(new \App\Model\Work\Procedure\Entity\Id($command->procedure_id));

        if ($procedure->getOrganizer()->getUser()->getId()->getValue() !== $command->userId){
            throw new \DomainException("403 access");
        }

        $lot->paymentWinnerConfirm();
        $procedure->stopOfTrading();;
        $this->flusher->flush();
    }

    public function handleAnnulled(Command $command){
        $lot = $this->lotRepository->get(new Id($command->lot_id));
        $procedure = $this->procedureRepository->get(new \App\Model\Work\Procedure\Entity\Id($command->procedure_id));
        $lot->paymentWinnerAnnulled();
        $procedure->failed();;
        $this->flusher->flush();
    }



}