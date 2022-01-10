<?php
declare(strict_types=1);

namespace App\Command\Cron\Auction;

use App\Container\Model\HostService;
use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Lot\Auction\AuctionRepository;
use App\Model\Work\Procedure\UseCase\Auction\Close\Handler;
use App\ReadModel\Auction\AuctionFetcher;
use App\Services\Tasks\Notification;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CompletedCommand
 * @package App\Command\Cron\Auction
 */
class CompletedCommand extends Command
{

    /**
     * @var AuctionFetcher
     */
    private $auctionFetcher;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var AuctionRepository
     */
    private $auctionRepository;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var HostService
     */
    private $hostService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * IndexCommand constructor.
     * @param AuctionFetcher $auctionFetcher
     * @param Handler $handler
     * @param AuctionRepository $auctionRepository
     * @param Notification $notificationService
     * @param UrlGeneratorInterface $urlGenerator
     * @param Flusher $flusher
     * @param HostService $hostService
     * @param LoggerInterface $logger
     */
    public function __construct(
        AuctionFetcher $auctionFetcher,
        Handler $handler,
        Flusher $flusher,
        AuctionRepository $auctionRepository,
        Notification $notificationService,
        UrlGeneratorInterface $urlGenerator,
        HostService $hostService,
        LoggerInterface $logger
    )
    {
        $this->auctionFetcher = $auctionFetcher;
        $this->handler = $handler;
        $this->flusher = $flusher;
        $this->auctionRepository = $auctionRepository;
        $this->urlGenerator = $urlGenerator;
        $this->notificationService = $notificationService;
        $this->hostService = $hostService;
        $this->logger = $logger;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->setName('cron:auction:completed')->setDescription('Auctions check');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $findAuctions = $this->auctionFetcher->findAllByStatusActive();

        if ($findAuctions !== null) {

            foreach ($findAuctions as $row) {

                $auction = $this->auctionRepository->get(new \App\Model\Work\Procedure\Entity\Lot\Auction\Id($row['id']));


                $showProcedureUrl = $this->hostService->getBaseUrl().
                    $this->urlGenerator->generate('procedure.show', [
                        'procedureId' => $auction->getLot()->getProcedure()->getId()
                    ]);

                $message = Message::organizerCompletedAuction(
                    $auction->getLot()->getProcedure()->getOrganizer()->getUser()->getEmail(),
                    $auction->getLot()->getProcedure()->getIdNumber(),
                    $showProcedureUrl
                );

                $this->notificationService->emailToOneUser($message);
                $this->notificationService->sendToOneUser($auction->getLot()->getProcedure()->getOrganizer()->getUser(), $message);

                $participants = [];

                foreach ($auction->getLot()->getApprovedBids() as $bid){
                    $participants[] = $bid->getParticipant()->getUser();
                }

                $message = Message::completedAuction(
                    $auction->getLot()->getProcedure()->getOrganizer()->getUser()->getEmail(),
                    $auction->getLot()->getProcedure()->getIdNumber(),
                    $showProcedureUrl
                );

                $this->notificationService->emailToMultipleUsers($participants, $message);
                $this->notificationService->sendToMultipleUsers($participants, $message);


                $auction->completedAuction();
            }
            $this->flusher->flush();
            $output->writeln('<info>Done!</info>');
        }

        return 1;
    }

}