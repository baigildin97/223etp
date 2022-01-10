<?php
declare(strict_types=1);

namespace App\Controller\Admin\Moderator;


use App\Model\User\Entity\Profile\Payment\Transaction\Status;
use App\ReadModel\Profile\Payment\Transaction\Filter\Filter;
use App\ReadModel\Profile\Payment\Transaction\TransactionFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PaymentController
 * @package App\Controller\Admin\Moderator
 * @IsGranted("ROLE_MODERATOR")
 */
class PaymentController extends AbstractController
{
    private const PER_PAGE = 10;

    private $logger;
    private $translator;
    private $transactionFetcher;

    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        TransactionFetcher $transactionFetcher
    ) {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->transactionFetcher = $transactionFetcher;
    }

    /**
     * Показывает ожидающие и исполненные транзакции
     * @param Request $request
     * @return Response
     * @Route("/moderator/payment", name="payment.moderator")
     */
    public function index(Request $request): Response{

        $filter = Filter::forStatus([
            Status::STATUS_PENDING
        ]);

        $transactions = $this->transactionFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );



        return $this->render('app/admin/moderator/payment/index.html.twig', [
            'transactions' => $transactions
        ]);
    }

}
