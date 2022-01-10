<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\Edit;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Requisite\RequisiteRepository;
use App\Model\Work\Procedure\Entity\Lot\Id;
use App\Model\Work\Procedure\Entity\Lot\IdNumber;
use App\Model\Work\Procedure\Entity\Lot\LotRepository;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use Money\Money;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var LotRepository
     */
    private $lotRepository;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @var RequisiteRepository
     */
    private $requisiteRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param LotRepository $lotRepository
     * @param ProfileRepository $profileRepository
     * @param RequisiteRepository $requisiteRepository
     */
    public function __construct(Flusher $flusher,
                                LotRepository $lotRepository,
                                ProfileRepository $profileRepository,
                                RequisiteRepository $requisiteRepository
    )
    {
        $this->flusher = $flusher;
        $this->lotRepository = $lotRepository;
        $this->profileRepository = $profileRepository;
        $this->requisiteRepository = $requisiteRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        $lot = $this->lotRepository->get(new Id($command->lotId));
        $organizerProfile = $this->profileRepository->getByUser(new \App\Model\User\Entity\User\Id($command->userId));
        if ($lot->getProcedure()->getOrganizer()->getId()->getValue() !== $organizerProfile->getId()->getValue()) {
            throw new \DomainException('Access Denied');
        }

       // $requisite = $this->requisiteRepository->get(new \App\Model\User\Entity\Profile\Requisite\Id($command->requisite));

        $lot->edit(
            $command->idNumber,
            $command->arrestedPropertyType,
            new Reload($command->reloadLot),
            $command->tenderBasic,
            new Nds($command->nds),
            $command->pledgeer,
            $command->bailiffsName,
            $command->executiveProductionNumber,
            $command->offerAuctionTime,
            Money::RUB($command->auctionStep),
            new \DateTimeImmutable($command->startDateOfApplications),
            new \DateTimeImmutable($command->closingDateOfApplications),
            new \DateTimeImmutable($command->summingUpApplications),
            new \DateTimeImmutable($command->startTradingDate),
            $command->debtorFullName,
            $command->debtorFullNameDateCase,
            $command->requisite,
            $command->lotName,
            $command->region,
            $command->location_object,
            $command->additional_object_characteristics,
            Money::RUB($command->starting_price)
        );

        $this->flusher->flush();
    }

}