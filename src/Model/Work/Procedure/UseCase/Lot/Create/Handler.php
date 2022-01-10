<?php


namespace App\Model\Work\Procedure\UseCase\Lot\Create;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Services\Procedure\Lot\NumberGenerator;
use Money\Money;

class Handler
{
    private $procedureRepository;
    private $numberGeneratorLot;
    private $flusher;

    public function __construct(ProcedureRepository $procedureRepository,
                                NumberGenerator $numberGeneratorLot,
                                Flusher $flusher)
    {
        $this->procedureRepository = $procedureRepository;
        $this->numberGeneratorLot = $numberGeneratorLot;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $procedure = $this->procedureRepository->get(new Id($command->procedure_id));

        if (new \DateTime() >= new \DateTime($command->startTradingDate)){
            throw new \DomainException("Начало торгов не может быть, ранее сегодняшнего дня.");
        }

        if (new \DateTime($command->startDateOfApplications) >= new \DateTime($command->closingDateOfApplications)){
            throw new \DomainException("Дата началы подачи заявок должно быть меньше, чем даты окончание подачи заявок");
        }

        if (new \DateTime($command->closingDateOfApplications) >= new \DateTime($command->summingUpApplications)){
            throw new \DomainException("Дата окончание подачи заявок должно быть меньше, чем даты подведения итогов заявок");
        }

        if (new \DateTime($command->summingUpApplications) >= new \DateTime($command->startTradingDate)){
            throw new \DomainException("Дата и время подведения итогов приема заявок должно быть меньше, чем дата и время проведения торгов");
        }

        $procedure->addLot(
            $this->numberGeneratorLot->next(),
            $command->arrestedPropertyType,
            new Reload($command->reloadLot),
            $command->tenderBasic,
            new Nds($command->nds),
            $command->dateEnforcementProceedings,
            new \DateTimeImmutable($command->startDateOfApplications),
            new \DateTimeImmutable($command->closingDateOfApplications),
            new \DateTimeImmutable($command->summingUpApplications),
            $command->debtorFullName,
            $command->debtorFullNameDateCase,
            new \DateTimeImmutable($command->advancePaymentTime),
            $command->requisite,
            $command->lotName,
            $command->region,
            $command->location_object,
            $command->additional_object_characteristics,
            Money::RUB($command->starting_price),
            Money::RUB($command->deposit_amount),
            $command->depositPolicy,
            $command->bailiffsName,
            $command->bailiffsNameDativeCase,
            $command->pledgeer,
            $command->executiveProductionNumber,
            $command->clientIp,
            $command->offerAuctionTime,
            Money::RUB($command->auctionStep),
            new \DateTimeImmutable($command->startTradingDate)
        );

        $this->flusher->flush();
    }
}