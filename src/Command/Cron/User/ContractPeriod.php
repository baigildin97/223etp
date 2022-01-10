<?php
declare(strict_types=1);

namespace App\Command\Cron\User;

use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\Profile\Id;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Services\Tasks\Notification;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ContractPeriod
 * @package App\Command\Cron\User
 */
class ContractPeriod extends Command
{
    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    protected function configure(): void
    {
        $this->setName('cron:user:contract')->setDescription('Verification of the term of the contract');
    }

    /**
     * ContractPeriod constructor.
     * @param ProfileFetcher $profileFetcher
     * @param ProfileRepository $profileRepository
     * @param Flusher $flusher
     * @param Notification $notificationService
     * @param SettingsFetcher $settingsFetcher
     */
    public function __construct(ProfileFetcher $profileFetcher,
                                ProfileRepository $profileRepository,
                                Flusher $flusher,
                                Notification $notificationService,
                                SettingsFetcher $settingsFetcher
    ){
        $this->profileFetcher = $profileFetcher;
        $this->profileRepository = $profileRepository;
        $this->flusher = $flusher;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
        parent::__construct();
    }

    /**
     * Проверка пользователей на срок действия договора
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $findSignedProfiles = $this->profileFetcher->findSignedProfiles();
        $findNameOrganization = $this->settingsFetcher->findDetailByKey(Key::nameOrganization());
        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());

        if ($findSignedProfiles) {
            foreach ($findSignedProfiles as $signedProfile) {
                if (new \DateTimeImmutable($signedProfile['contract_period']) <= new \DateTimeImmutable()) {
                    $profile = $this->profileRepository->get(new Id($signedProfile['id']));
                    $profile->cancellationContract();
                    $this->flusher->flush();

                    $message = Message::cancellationContract($profile->getUser()->getEmail(), $findNameOrganization);

                    $this->notificationService->emailToOneUser($message);
                    $this->notificationService->sendToOneUser($profile->getUser(), $message);
                }
            }
        }

        $output->writeln('<info>Done!</info>');

        return 1;
    }
}