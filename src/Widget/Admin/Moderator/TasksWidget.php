<?php
declare(strict_types=1);

namespace App\Widget\Admin\Moderator;


use App\Model\User\Entity\Profile\Status;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Profile\Payment\Transaction\TransactionFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TasksWidget extends AbstractExtension
{

    private $procedureFetcher;
    private $profileFetcher;
    private $transactionFetcher;
    private $tokenStorage;

    public function __construct(
        ProcedureFetcher $procedureFetcher,
        ProfileFetcher $profileFetcher,
        TransactionFetcher $transactionFetcher,
        TokenStorageInterface $tokenStorage
    ){
        $this->procedureFetcher = $procedureFetcher;
        $this->profileFetcher = $profileFetcher;
        $this->transactionFetcher = $transactionFetcher;
        $this->tokenStorage = $tokenStorage;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('tasks', [$this, 'tasks'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function tasks(Environment $twig)
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return '';
        }

        if (!($userIdentity = $token->getUser()) instanceof UserIdentity) {
            return '';
        }
        $userId = $userIdentity->getId();

        $countModerateProcedures = $this->procedureFetcher->countModerateProcedures();
        $countModeratorProcessingProcedures = $this->procedureFetcher->countModerateProceduresProcessing($userId);

        $countPendingTransactions = $this->transactionFetcher->countByStatusPending();


        $countModerateProfiles = $this->profileFetcher->countModerateProfile();
        $countModeratorProcessingProfile = $this->profileFetcher->countModerateProfileProcessing($userId);

        return $twig->render('widget/admin/moderator/tasks.html.twig', [
                'count_moderate_profiles' => $countModerateProfiles,
                'count_moderator_processing_profile' => $countModeratorProcessingProfile,

                'count_moderate_procedures' => $countModerateProcedures,
                'count_moderator_processing_procedures' => $countModeratorProcessingProcedures,

                'count_pending_transactions' => $countPendingTransactions,
                'userId' => $userId
            ]
        );
    }
}
