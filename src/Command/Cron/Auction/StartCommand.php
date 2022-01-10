<?php
declare(strict_types=1);

namespace App\Command\Cron\Auction;

use App\Container\Model\HostService;
use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Lot\Auction\AuctionRepository;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\ReadModel\Auction\AuctionFetcher;
use App\Services\Tasks\Notification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class StartCommand
 * @package App\Command\Cron\Auction
 */
class StartCommand extends Command
{

    /**
     * @var AuctionFetcher
     */
    private $auctionFetcher;

    /**
     * @var ProcedureRepository
     */
    private $procedureRepository;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var AuctionRepository
     */
    private $auctionRepository;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var HostService
     */
    private $hostService;

    private $urlGenerator;

    /**
     * StartCommand constructor.
     * @param AuctionFetcher $auctionFetcher
     * @param Flusher $flusher
     * @param ProcedureRepository $procedureRepository
     * @param AuctionRepository $auctionRepository
     * @param Notification $notificationService
     * @param HostService $hostService
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        AuctionFetcher $auctionFetcher,
        Flusher $flusher,
        ProcedureRepository $procedureRepository,
        AuctionRepository $auctionRepository,
        Notification $notificationService,
        HostService $hostService,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->auctionFetcher = $auctionFetcher;
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->auctionRepository = $auctionRepository;
        $this->notificationService = $notificationService;
        $this->hostService = $hostService;
        $this->urlGenerator = $urlGenerator;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('cron:auction:start')->setDescription('Auctions check');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $findAuctions = $this->auctionFetcher->findAllByStatusWait();

        if ($findAuctions !== null) {
            foreach ($findAuctions as $row) {
                $auction = $this->auctionRepository->get(new \App\Model\Work\Procedure\Entity\Lot\Auction\Id($row['id']));
                $organizer = $auction->getLot()->getProcedure()->getOrganizer()->getUser();
                $participants = [];

                foreach ($auction->getLot()->getApprovedBids() as $bid){
                    $participants[] = $bid->getParticipant()->getUser();
                }

                $showProcedureUrl = $this->hostService->getBaseUrl().
                    $this->urlGenerator->generate('procedure.show', ['procedureId' => $auction->getLot()->getProcedure()->getId()->getValue()]);

                $message = Message::startAuction($organizer->getEmail(), $showProcedureUrl, $auction->getLot()->getProcedure()->getIdNumber());

                $this->notificationService->emailToOneUser($message);
                $this->notificationService->sendToOneUser($organizer, $message);

                $this->notificationService->emailToMultipleUsers($participants, $message);
                $this->notificationService->sendToMultipleUsers($participants, $message);

                $auction->startAuction();
            }
            $this->flusher->flush();
            $output->writeln('<info>Done!</info>');
        }
        return 1;
    }

}