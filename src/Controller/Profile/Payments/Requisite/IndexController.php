<?php
declare(strict_types=1);

namespace App\Controller\Profile\Payments\Requisite;


use App\Model\User\UseCase\Profile\Payment\Requisite\Add;
use App\Model\User\UseCase\Profile\Payment\Requisite\Archived\Command;
use App\Model\User\UseCase\Profile\Payment\Requisite\Archived\Handler;
use App\Model\User\UseCase\Profile\Payment\Requisite\Edit;
use App\ReadModel\Profile\Payment\PaymentFetcher;
use App\ReadModel\Profile\Payment\Requisite\Filter\Filter;
use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Security\Voter\PaymentAccess;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class IndexController
 * @package App\Controller\Profile\Payments\Requisite
 */
class IndexController extends AbstractController
{

    private const PER_PAGE = 10;

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var PaymentFetcher
     */
    private $paymentFetcher;

    /**
     * @var RequisiteFetcher
     */
    private $requisiteFetcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * IndexController constructor.
     * @param ProfileFetcher $profileFetcher
     * @param PaymentFetcher $paymentFetcher
     * @param RequisiteFetcher $requisiteFetcher
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProfileFetcher $profileFetcher,
        PaymentFetcher $paymentFetcher,
        RequisiteFetcher $requisiteFetcher,
        TranslatorInterface $translator,
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->profileFetcher = $profileFetcher;
        $this->paymentFetcher = $paymentFetcher;
        $this->requisiteFetcher = $requisiteFetcher;
    }

    /**
     * @param Request $request
     * @param string $payment_id
     * @return Response
     * @Route("/payment/{payment_id}/requisites", name="payment.requisites")
     */
    public function index(Request $request, string $payment_id): Response
    {
        $payment = $this->paymentFetcher->findDetail($payment_id);

     //   $this->denyAccessUnlessGranted(
     //       PaymentAccess::PAYMENT_SHOW,
     //       $payment->profile_id
     //   );

        $pagination = $this->requisiteFetcher->all(
            Filter::fromPayment($payment_id),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );


        return $this->render('app/profile/payment/requisite/index.html.twig', [
            'requisites' => $pagination,
            'payment' => $payment
        ]);
    }

    /**
     * @param Request $request
     * @param string $payment_id
     * @param Add\Handler $handler
     * @return Response
     * @Route("/payment/{payment_id}/requisite/add", name="payment.requisite.add")
     */
    public function add(Request $request, string $payment_id, Add\Handler $handler): Response
    {

        $payment = $this->paymentFetcher->findDetail($payment_id);

   //     $this->denyAccessUnlessGranted(
     //       PaymentAccess::PAYMENT_SHOW,
     //       $payment->profile_id
     //   );

        $form = $this->createForm(Add\Form::class, $command = new Add\Command($payment_id));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Requisite added successfully.', [], 'exceptions'));
                return $this->redirectToRoute('payment.requisites', ['payment_id' => $payment_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }
        return $this->render('app/profile/payment/requisite/add.html.twig', [
            'payment' => $this->paymentFetcher->findDetail($payment_id),
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $payment_id
     * @param string $requisite_id
     * @param Edit\Handler $handler
     * @return Response
     * @Route("/payment/{payment_id}/requisite/{requisite_id}/edit", name="payment.requisite.edit")
     */
    public function edit(Request $request, string $payment_id, string $requisite_id, Edit\Handler $handler): Response
    {

        $payment = $this->paymentFetcher->findDetail($payment_id);

      //  $this->denyAccessUnlessGranted(
       //     PaymentAccess::PAYMENT_SHOW,
        //    $payment->profile_id
     //   );

        $requisite = $this->requisiteFetcher->findDetail($requisite_id);
        $form = $this->createForm(Edit\Form::class,
            $command = Edit\Command::me(
                $payment_id,
                $requisite_id,
                $requisite->bank_name,
                $requisite->bank_bik,
                $requisite->payment_account,
                $requisite->bank_address,
                $requisite->personal_account,
                $requisite->correspondent_account

            )
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Requisite edit successfully.', [], 'exceptions'));
                return $this->redirectToRoute('payment.requisites', ['payment_id' => $payment_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }
        return $this->render('app/profile/payment/requisite/edit.html.twig', [
            'payment' => $this->paymentFetcher->findDetail($payment_id),
            'form' => $form->createView()
        ]);
    }


    /**
     * @param Request $request
     * @param string $payment_id
     * @param string $requisite_id
     * @param Handler $handler
     * @return Response
     * @Route("payment/{payment_id}/requisite/{requisite_id}/arcived", name="payment.requisite.archived", methods={"POST"})
     */
    public function archived(Request $request, string $payment_id, string $requisite_id, Handler $handler): Response
    {
        $payment = $this->paymentFetcher->findDetail($payment_id);

     //   $this->denyAccessUnlessGranted(
       //     PaymentAccess::PAYMENT_SHOW,
      //      $payment->profile_id
     //   );

        $command = new Command($requisite_id);
        try {
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Requisite archived successfully.', [], 'exceptions'));
            return $this->redirectToRoute('payment.requisites', ['payment_id' => $payment_id]);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }
    }

}