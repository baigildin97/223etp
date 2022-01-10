<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Edit;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Lot\LotRepository;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use Money\Money;

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
     * @var LotRepository
     */
    private $lotRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param ProcedureRepository $procedureRepository
     * @param ProfileRepository $profileRepository
     */
    public function __construct(Flusher $flusher,
                                ProcedureRepository $procedureRepository,
                                ProfileRepository $profileRepository,
    LotRepository $lotRepository

    ){
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->profileRepository = $profileRepository;
        $this->lotRepository = $lotRepository;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void{
        $procedure = $this->procedureRepository->get(new Id($command->procedureId));

        $lot = $this->lotRepository->getByProcedureId($procedure->getId());

        $profileOrganizer = $this->profileRepository->getByUser(new \App\Model\User\Entity\User\Id($command->userId));
        if ($profileOrganizer->getId()->getValue() !== $procedure->getOrganizer()->getId()->getValue()){
            throw new \DomainException('Access Denied');
        }


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


        $lot->edit(
            $command->procedureName,
            $command->infoPointEntry,
            $command->infoTradingVenue,
            $command->infoBiddingProcess,
            $command->arrestedPropertyType,
            new Reload($command->reloadLot),
            $command->tenderBasic,
            new Nds($command->nds),
            $command->pledgeer,
            $command->bailiffsName,
			$command->bailiffsNameDativeCase,
            $command->dateEnforcementProceedings,
            $command->executiveProductionNumber,
            Money::RUB($command->auctionStep),
            new \DateTimeImmutable($command->startDateOfApplications),
            new \DateTimeImmutable($command->closingDateOfApplications),
            new \DateTimeImmutable($command->summingUpApplications),
            new \DateTimeImmutable($command->startTradingDate),
            $command->debtorFullName,
            $command->debtorFullNameDateCase,
            new \DateTimeImmutable($command->advancePaymentTime),
            $command->requisite,
            $command->procedureName,
            $command->region,
            $command->location_object,
            $command->additional_object_characteristics,
            Money::RUB($command->starting_price),
            Money::RUB($command->deposit_amount),
            $command->depositPolicy,
            $command->offerAuctionTime,
            $command->tenderingProcess,
            $command->clientIp,

            $command->organizerFullName,
            $command->organizerEmail,
            $command->organizerPhone
        );

        $this->flusher->flush();
    }

}