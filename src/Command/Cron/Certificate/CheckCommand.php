<?php


namespace App\Command\Cron\Certificate;


use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Certificate\Id;
use App\ReadModel\Certificate\CertificateFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckCommand
 * @package App\Command\Cron\Certificate
 */
class CheckCommand extends Command
{
    /**
     * @var CertificateFetcher
     */
    private $certificateFetcher;

    /**
     * @var CertificateRepository
     */
    private $certificateRepository;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * CheckCommand constructor.
     * @param CertificateFetcher $certificateFetcher
     * @param CertificateRepository $certificateRepository
     * @param Flusher $flusher
     */
    public function __construct(
        CertificateFetcher $certificateFetcher,
        CertificateRepository $certificateRepository,
        Flusher $flusher
    ){
        $this->certificateFetcher = $certificateFetcher;
        $this->certificateRepository = $certificateRepository;
        $this->flusher = $flusher;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('cron:certificate:check')->setDescription('Certificates check');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $certificates = $this->certificateFetcher->allForCheck();
        if ($certificates !== null) {
            foreach ($certificates as $certificate) {
                $cert = $this->certificateRepository->get(new Id($certificate['id']));
                $cert->archived();
                $this->flusher->flush();
            }
            $output->writeln('<info>Done!</info>');
        }
        return 1;
    }
}