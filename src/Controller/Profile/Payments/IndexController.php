<?php
declare(strict_types=1);
namespace App\Controller\Profile\Payments;


use App\Model\User\Service\Profile\Payment\Withdraw\WithdrawXmlGenerator;
use App\Model\User\UseCase\Profile\Payment\Transaction\Confirm;
use App\Model\User\UseCase\Profile\Payment\Transaction\Deposit;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Profile\Payment\Filter\Filter;
use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use \App\ReadModel\Profile\Payment\Transaction\Filter\Filter as TransactionFilter;
use App\ReadModel\Profile\Payment\PaymentFetcher;
use App\ReadModel\Profile\Payment\Transaction\TransactionFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Security\Voter\PaymentAccess;
use App\Security\Voter\ProfileAccess;
use App\Services\Hash\Streebog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Money\Currency;
use Money\Money;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\User\UseCase\Profile\Payment\Transaction\Cancel;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw;

/**
 * Class IndexController
 * @package App\Controller\Profile\Payments
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
     * @var PaymentFetcher
     */
    private $paymentFetcher;

    /**
     * @var TransactionFetcher
     */
    private $transactionFetcher;

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var RequisiteFetcher
     */
    private $requisiteFetcher;

    /**
     * IndexController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param PaymentFetcher $paymentFetcher
     * @param TransactionFetcher $transactionFetcher
     * @param ProfileFetcher $profileFetcher
     * @param RequisiteFetcher $requisiteFetcher
     */
    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        PaymentFetcher $paymentFetcher,
        TransactionFetcher $transactionFetcher,
        ProfileFetcher $profileFetcher,
        RequisiteFetcher $requisiteFetcher
    ) {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->paymentFetcher = $paymentFetcher;
        $this->transactionFetcher = $transactionFetcher;
        $this->profileFetcher = $profileFetcher;
        $this->requisiteFetcher = $requisiteFetcher;
    }


    /**
     * @param Request $request
     * @param string $payment_id
     * @return Response
     * @Route("/payment/{payment_id}", name="payment.show")
     */
    public function show(Request $request, string $payment_id): Response {
        $payment = $this->paymentFetcher->findDetail($payment_id);
        //$this->denyAccessUnlessGranted(
         //   PaymentAccess::PAYMENT_SHOW,
      //      $payment->profile_id
       // );


        $transactions = $this->transactionFetcher->all(
            TransactionFilter::fromPayment($payment_id),
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/profile/payment/show.html.twig', [
            'payment' => $payment,
            'transactions' => $transactions
        ]);
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @Route("/payments/{profile_id}", name="payments")
     */
    public function index(Request $request, string $profile_id, ProfileFetcher $profileFetcher): Response {

     //   $this->denyAccessUnlessGranted(
        //    PaymentAccess::PAYMENT_SHOW,
        //    $profile_id
    //    );
//        if (!$profileFetcher->existsActiveProfileByUser($userId = $this->getUser()->getId())){
//            $this->addFlash('success', $this->translator->trans('Your profile has not yet been accredited or completed.',[],'exceptions'));
//            return $this->redirectToRoute('profile');
//        }

//        $profile = $profileFetcher->findDetailByUserId($user_id);
        $pagination = $this->paymentFetcher->all(
            Filter::fromProfile($profile_id),
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/profile/payment/index.html.twig', [
            'payments' => $pagination,
        ]);
    }

    /**
     * @param Request $request
     * @param string $payment_id
     * @param Deposit\Handler $handler
     * @return Response
     * @Route("/payment/{payment_id}/deposit", name="payment.deposit")
     */
    public function deposit(Request $request, string $payment_id, Deposit\Handler $handler): Response {

        $profile = $this->profileFetcher->findDetailByUserId($this->getUser()->getId());
        if ($this->isGranted('ROLE_MODERATOR')){
            $requisite = $this->requisiteFetcher->existsActiveRequisiteForPaymentId($payment_id);
        }else{
            $requisite = $this->requisiteFetcher->existsActiveRequisiteForPaymentId($profile->payment_id);
        }
        $payment = $this->paymentFetcher->findDetail($payment_id);
   //     $this->denyAccessUnlessGranted(
    //        PaymentAccess::PAYMENT_SHOW,
     //       $payment->profile_id
    //    );


        if (!$requisite){
            $this->addFlash('error',  'Необходимо добавить реквизиты.');
            return $this->redirectToRoute('payment.show',['payment_id' => $payment_id]);
        }
        $form = $this->createForm(Deposit\Form::class, $command = new Deposit\Command($payment_id), [
            'payment_id' => $payment_id
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                $this->addFlash('success',  $this->translator->trans('Transaction added successfully.',[],'exceptions'));
                return $this->redirectToRoute('payment.show', ['payment_id'=>$payment_id]);
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/payment/deposit.html.twig', [
            'form' => $form->createView(),
            'payment' => $this->paymentFetcher->findDetail($payment_id)
        ]);
    }

    /**
     * @param Request $request
     * @param string $payment_id
     * @return Response
     * @Route("/payment/{payment_id}/withdraw", name="payment.withdraw")
     */
    public function withdraw(Request $request, string $payment_id): Response {
        $profile = $this->profileFetcher->findDetailByUserId($this->getUser()->getId());

        if ($this->isGranted('ROLE_MODERATOR')){
            $requisite = $this->requisiteFetcher->existsActiveRequisiteForPaymentId($payment_id);
        }else{
            $requisite = $this->requisiteFetcher->existsActiveRequisiteForPaymentId($profile->payment_id);
        }

        $payment = $this->paymentFetcher->findDetail($payment_id);
    //    $this->denyAccessUnlessGranted(
     //       PaymentAccess::PAYMENT_SHOW,
      //      $payment->profile_id
     //   );

        if (!$requisite){
            $this->addFlash('error',  'Необходимо добавить реквизиты.');
            return $this->redirectToRoute('payment.show',['payment_id' => $payment_id]);
        }


        $form = $this->createForm(Withdraw\Form::class, $command = new Withdraw\Command($payment_id), [
            'payment_id' => $payment_id
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $transformer = new MoneyToLocalizedStringTransformer();
            try{
                return $this->redirectToRoute('payment.withdraw.xml', [
                    'payment_id'=>$payment_id,
                    'requisite_id' => $request->request->get('form')['requisite'],
                    'money' => $request->request->get('form')['money']
                ]);
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/payment/withdraw.html.twig', [
            'form' => $form->createView(),
            'payment' => $this->paymentFetcher->findDetail($payment_id)
        ]);
    }

    /**
     * @param Request $request
     * @param string $payment_id
     * @param string $requisite_id
     * @param string $money
     * @param WithdrawXmlGenerator $withdrawXmlGenerator
     * @return Response
     * @Route("/payment/{payment_id}/withdraw/xml/requisite/{requisite_id}/money/{money}", name="payment.withdraw.xml")
     */
    public function withdrawXmlDocument(Request $request, string $payment_id, string $requisite_id, string $money, Withdraw\Sign\Handler $handler, RequisiteFetcher $requisiteFetcher, WithdrawXmlGenerator $withdrawXmlGenerator, Streebog $stribog, ProfileFetcher $profileFetcher): Response {
        $convertMoney = (string)($money * 100);
        $profile = $profileFetcher->findDetailByUserId($this->getUser()->getId());


        $payment = $this->paymentFetcher->findDetail($payment_id);
      //  $this->denyAccessUnlessGranted(
       //     PaymentAccess::PAYMENT_SHOW,
       //     $payment->profile_id
      //  );

        $requisite = $requisiteFetcher->findDetail($requisite_id);
        $statementText = 'Прошу Вас осуществить вывод денежных средств с моего вируального счета №'.$payment->invoice_number.' Направленных ранее в качестве гарантийного обеспечения, согласно Регламента ЭТП РесТорг';

        $normalize = new Withdraw\Sign\Normalize(
            $statementText,
            $convertMoney,
            $profile->getOwnerFullName(),
            $profile->certificate_subject_name_inn,
            $profile->kpp,
            $requisite->bank_name,
            $requisite->payment_account,
            $requisite->correspondent_account,
            $requisite->bank_bik
        );

        $xml = $withdrawXmlGenerator->generate($normalize);
        //dd($profile);

        $command = new  Withdraw\Sign\Command($xml, $stribog->getHash($xml), $requisite_id, $payment_id, $convertMoney);
        $form = $this->createForm(Withdraw\Sign\Form::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                return $this->redirectToRoute('payment.show', ['payment_id'=>$payment_id]);
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/payment/withdraw/sign.html.twig', [
            'payment' => $payment,
            'form' => $form->createView(),
            'statementText' => $statementText,
            'profile' => $profile,
            'requisite' => $requisite,
            'money' => new Money($convertMoney, new Currency('RUB'))
        ]);
    }

    /**
     * @param Request $request
     * @param string $payment_id
     * @param string $transaction_id
     * @param Cancel\Handler $handler
     * @return Response
     * @Route("payment/{payment_id}/transaction/{transaction_id}/cancel", name="transaction.cancel", methods={"POST"})
     */
    public function cancel(Request $request, string $payment_id, string $transaction_id, Cancel\Handler $handler):Response {
        $command = new Cancel\Command($transaction_id, $payment_id, $this->getUser()->getId());
        try {
            $handler->handle($command);
        }catch (\DomainException $e){
            $this->logger->error($e->getMessage(), ['exception'=>$e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
        }

        return $this->redirectToRoute('payment.show', ['payment_id' => $payment_id]);
    }


    /**
     * @param Request $request
     * @param string $payment_id
     * @param string $transaction_id
     * @param SettingsFetcher $settingsFetcher
     * @return Response
     * @Route("payment/{payment_id}/transaction/{transaction_id}/invoice", name="payment.invoice")
     */
    public function invoice(Request $request, string $payment_id, string $transaction_id, SettingsFetcher $settingsFetcher): Response{
        $payment = $this->paymentFetcher->findDetail($payment_id);
    //    $this->denyAccessUnlessGranted(
    //        PaymentAccess::PAYMENT_SHOW,
     //       $payment->profile_id
     //   );

        $setting = $settingsFetcher->allArray();


        $transaction = $this->transactionFetcher->findDetail($transaction_id);

        return $this->render('app/profile/payment/invoice.html.twig', [
            'payment' => $payment,
            'transaction' => $transaction,
            'setting' => $setting
        ]);
    }

    /**
     * @param Request $request
     * @param string $payment_id
     * @param string $transaction_id
     * @param Confirm\Handler $handler
     * @return Response
     * @IsGranted("ROLE_MODERATOR")
     * @Route("payment/{payment_id}/transaction/{transaction_id}/confirm", name="transaction.confirm", methods={"POST"})
     */
    public function confirm(Request $request, string $payment_id, string $transaction_id, Confirm\Handler $handler): Response
    {
        $command = new Confirm\Command($transaction_id, $payment_id);
        try {
            $handler->handle($command);
            $this->addFlash('success', 'Завяка успешно одобрена');
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }

        return $this->redirectToRoute('payment.show', ['payment_id' => $payment_id]);
    }

}
