<?php
declare(strict_types=1);

namespace App\Controller\Admin\Dashboard;

use App\Helpers\FormatMoney;
use App\ReadModel\Admin\Dashboard\DashboardFetcher;
use App\ReadModel\Admin\Settings\Filter\Filter;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use Doctrine\DBAL\Exception;
use Money\Currency;
use Money\Money;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class IndexController
 * @package App\Controller\Admin\Settings
 * @IsGranted("ROLE_MODERATOR")
 */
class IndexController extends AbstractController
{
    private const PER_PAGE = 10;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormatMoney
     */
    private $formatMoney;

    /**
     * IndexController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param FormatMoney $formatMoney
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, FormatMoney $formatMoney)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->formatMoney = $formatMoney;
    }

    /**
     * @param Request $request
     * @param DashboardFetcher $dashboardFetcher
     * @return Response
     * @throws Exception
     * @Route("/admin/dashboard", name="admin.dashboard")
     */
    public function index(Request $request, DashboardFetcher $dashboardFetcher): Response
    {
        $countAllUsers = $dashboardFetcher->countAllUsers();
        $countActiveProfileUsers = $dashboardFetcher->countActiveProfileUsers();
        $countModerateProfileUsers = $dashboardFetcher->countModerateProfileUsers();
        $countBlockedProfileUsers = $dashboardFetcher->countBlockedProfileUsers();

        $sumAvailableAmount = new Money($dashboardFetcher->sumAvailableAmount(), new Currency('RUB'));
        $sumBlockedAmount = new Money($dashboardFetcher->sumBlockedAmount(), new Currency('RUB'));
        $sumWithdrawalAmount = new Money($dashboardFetcher->sumWithdrawalAmount(), new Currency('RUB'));

        $countAllProcedures = $dashboardFetcher->countAllProcedures();
        $countModerateProcedures = $dashboardFetcher->countModerateProcedures();
        $countFailedProcedures = $dashboardFetcher->countFailedProcedures();

        $countAllBids = $dashboardFetcher->countAllBids();
        $countSentBids = $dashboardFetcher->countSentBids();
        $countApprovedBids = $dashboardFetcher->countApprovedBids();

        return $this->render('app/admin/dashboard/index.html.twig', [
            'count_all_users' => $countAllUsers,
            'count_active_profile_users' => $countActiveProfileUsers,
            'count_moderate_profile_users' => $countModerateProfileUsers,
            'count_blocked_profile_users' => $countBlockedProfileUsers,
            'sum_available_amount' =>  $this->formatMoney->convertMoneyToString($sumAvailableAmount),
            'sum_blocked_amount' =>  $this->formatMoney->convertMoneyToString($sumBlockedAmount),
            'sum_withdrawal_amount' => $this->formatMoney->convertMoneyToString($sumWithdrawalAmount),
            'count_all_procedures' => $countAllProcedures,
            'count_moderate_procedures' => $countModerateProcedures,
            'count_failed_procedures' => $countFailedProcedures,
            'count_all_bids' => $countAllBids,
            'count_sent_bids' => $countSentBids,
            'count_approved_bids' => $countApprovedBids
        ]);
    }
}