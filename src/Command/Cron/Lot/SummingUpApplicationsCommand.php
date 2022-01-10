<?php
declare(strict_types=1);
namespace App\Command\Cron\Lot;

use App\Container\Model\HostService;
use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Lot\LotRepository;
use App\Model\Work\Procedure\Entity\Lot\Status;
use App\ReadModel\Procedure\Lot\Filter\Filter;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\Services\Tasks\Notification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class StartCommand
 * @package App\Command\Cron\Auction
 */
class SummingUpApplicationsCommand extends Command{

    /**
     * @var LotFetcher
     */
    private $lotFetcher;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var LotRepository
     */
    private $lotRepository;

    /**
     * @var Notification
     */
    private $notificationService;

    private $urlGenerator;

    private $hostService;

    /**
     * Проверка на Дата подведение итогов приема заявок
     * IndexCommand constructor.
     * @param LotFetcher $lotFetcher
     * @param Flusher $flusher
     * @param LotRepository $lotRepository
     * @param Notification $notificationService
     * @param UrlGeneratorInterface $urlGenerator
     * @param HostService $hostService
     */
    public function __construct(
        LotFetcher $lotFetcher,
        Flusher $flusher,
        LotRepository $lotRepository,
        Notification $notificationService,
        UrlGeneratorInterface $urlGenerator,
        HostService $hostService
    ){
        $this->lotFetcher = $lotFetcher;
        $this->flusher = $flusher;
        $this->lotRepository = $lotRepository;
        $this->notificationService = $notificationService;
        $this->urlGenerator = $urlGenerator;
        $this->hostService = $hostService;
        parent::__construct();
    }

    protected function configure(): void{
        $this->setName('cron:lot:application:summing')->setDescription('Summing up applications');
    }

    public function execute(InputInterface $input, OutputInterface $output){
        $findLots = $this->lotFetcher->findByStatus(
            Filter::statusSummingUpApplications(new \DateTimeImmutable()),
            Status::applicationsReceived());
            if($findLots !== null) {
                foreach ($findLots as $lot) {
                    $lot = $this->lotRepository->get(new \App\Model\Work\Procedure\Entity\Lot\Id($lot['id']));
                    $lot->statusSummingUpApplications();

                    $showProcedureUrl = $this->hostService->getBaseUrl().
                        $this->urlGenerator->generate('procedure.show', [
                            'procedureId' => $lot->getProcedure()->getId()
                        ]);



                    $message = Message::organizerProtocolSummingRegProcedure(
                        $lot->getProcedure()->getOrganizer()->getUser()->getEmail(),
                        $lot->getFullNumber(),
                        $showProcedureUrl
                    );

                    $this->notificationService->emailToOneUser($message);
                    $this->notificationService->sendToOneUser($lot->getProcedure()->getOrganizer()->getUser(), $message);
                }

                $output->writeln('<info>Done!</info>');
            }
        return 1;
    }

}
