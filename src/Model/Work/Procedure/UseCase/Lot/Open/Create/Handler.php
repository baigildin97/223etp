<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\Create;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Requisite\RequisiteRepository;
use App\Model\Work\Procedure\Entity\Lot\Id;
use App\Model\Work\Procedure\Entity\Lot\Lot;
use App\Model\Work\Procedure\Entity\Lot\LotRepository;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\Model\Work\Procedure\Entity\Lot\Status;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\Lot\Auction\Id as AuctionId;
use Money\Money;
use App\Model\Work\Procedure\Entity\Id as ProcedureId;

class Handler
{
    private $flusher;

    private $procedureRepository;

    private $lotRepository;

    private $requisiteRepository;

    public function __construct(
        Flusher $flusher,
        ProcedureRepository $procedureRepository,
        LotRepository $lotRepository,
        RequisiteRepository $requisiteRepository
    )
    {
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->lotRepository = $lotRepository;
        $this->requisiteRepository = $requisiteRepository;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        $procedure = $this->procedureRepository->get(new ProcedureId($command->procedureId));

        if ($procedure->getLots()->count() >= 1) {
            throw new \DomainException('Lot already exists.');
        }

        if (new \DateTime() >= new \DateTime($command->startTradingDate)){
            throw new \DomainException("Начало торгов не может быть, ранее сегодняшнего дня.");
        }

        if (new \DateTime($command->summingUpApplications) >= new \DateTime($command->startTradingDate)){
            throw new \DomainException("Дата подведения итогов приема заявок должно быть меньше, чем даты началы торгов");
        }

        $procedure->addLot(
            $command->idNumber,
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
