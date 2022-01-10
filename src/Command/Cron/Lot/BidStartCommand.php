<?php
declare(strict_types=1);
namespace App\Command\Cron\Lot;

use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Lot\Status;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\ReadModel\Procedure\Lot\Filter\Filter;
use App\ReadModel\Procedure\Lot\LotFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StartCommand
 * @package App\Command\Cron\Auction
 */
class BidStartCommand extends Command{

    /**
     * @var LotFetcher
     */
    private $lotFetcher;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var ProcedureRepository
     */
    private $procedureRepository;

    /**
     * Проверка на Дата начала подачи заявок в лоте
     * IndexCommand constructor.
     * @param LotFetcher $lotFetcher
     * @param Flusher $flusher
     * @param ProcedureRepository $procedureRepository
     */
    public function __construct(LotFetcher $lotFetcher, Flusher $flusher, ProcedureRepository $procedureRepository){
        $this->lotFetcher = $lotFetcher;
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        parent::__construct();
    }

    protected function configure(): void{
        $this->setName('cron:lot:bid:start')->setDescription('Start receiving applications');
    }

    public function execute(InputInterface $input, OutputInterface $output){
        $findLots = $this->lotFetcher->findByStatus(
            Filter::startDateOfApplications(new \DateTimeImmutable()),
            Status::active());

//dd($findLots);

        if ($findLots){
            foreach ($findLots as $lot){
                $procedure = $this->procedureRepository->get(New Id($lot['procedure_id']));
                $procedure->acceptingApplications();
                $this->flusher->flush();
            }

            $output->writeln('<info>Done!</info>');
        }

        return 1;
    }

}