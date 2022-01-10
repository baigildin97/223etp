<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Create;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Requisite\Id;
use App\Model\User\Entity\Profile\Requisite\RequisiteRepository;
use App\Model\Work\Procedure\Entity\Lot\Bid;
use App\Model\Work\Procedure\Entity\Lot;
use \App\Model\User\Entity\User;
use App\Model\Work\Procedure\Services\Bid\NumberGenerator;
use App\ReadModel\Procedure\Bid\BidFetcher;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @var Lot\LotRepository
     */
    private $lotRepository;

    /**
     * @var RequisiteRepository
     */
    private $requisiteRepository;

    /**
     * @var BidFetcher
     */
    private $bidFetcher;

    /**
     * @var NumberGenerator
     */
    private $numberGenerator;

    /**
     * @var Bid\BidRepository
     */
    private $bidRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param ProfileRepository $profileRepository
     * @param Lot\LotRepository $lotRepository
     * @param RequisiteRepository $requisiteRepository
     * @param BidFetcher $bidFetcher
     * @param NumberGenerator $numberGenerator
     * @param Bid\BidRepository $bidRepository
     */
    public function __construct(
        Flusher $flusher,
        ProfileRepository $profileRepository,
        Lot\LotRepository $lotRepository,
        RequisiteRepository $requisiteRepository,
        BidFetcher $bidFetcher,
        NumberGenerator $numberGenerator,
        Bid\BidRepository $bidRepository
    )
    {
        $this->flusher = $flusher;
        $this->profileRepository = $profileRepository;
        $this->lotRepository = $lotRepository;
        $this->requisiteRepository = $requisiteRepository;
        $this->bidFetcher = $bidFetcher;
        $this->numberGenerator = $numberGenerator;
        $this->bidRepository = $bidRepository;
    }

    /**
     * @param Command $command
     * @throws \Doctrine\DBAL\Exception
     */
    public function handle(Command $command): void
    {
        $profile = $this->profileRepository->getByUser(new User\Id($command->userId));

        $bids = $this->bidFetcher->findAllWinningBidsByUser($profile->getId()->getValue());

        if ($bids) {
            foreach ($bids as $bidItem) {
                $bid = $this->bidRepository->get(new Bid\Id($bidItem['id']));
                if (!$bid->checkChargeback()) {
                    throw new \DomainException('У вас не оплаченный счет услуги оператора ЭТП!');
                }
            }
        }

        $lot = $this->lotRepository->get(new Lot\Id($command->lotId));

        if ($lot->getStatus()->getName() !== Lot\Status::acceptingApplications()->getName()) {
            throw new \DomainException("Прием заявок не начался");
        }

        $requisite = $this->requisiteRepository->get(new Id($command->requisite));

        if ($this->bidFetcher->existsActiveBidForLot($profile->getId()->getValue(), $lot->getId()->getValue())) {
            throw new \DomainException('Вы уже подали заявку на этот лот.');
        }

        $numberGenerator = $this->numberGenerator->next();

        $lot->addBid(
            $command->bidId,
            $numberGenerator,
            Bid\Status::new(),
            $profile,
            $command->clientIp,
            new \DateTimeImmutable(),
            $requisite,
            $profile->getSubscribeTariff()
        );

        $this->flusher->flush();
    }
}