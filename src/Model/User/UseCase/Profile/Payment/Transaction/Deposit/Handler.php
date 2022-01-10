<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Transaction\Deposit;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Payment\Id;
use App\Model\User\Entity\Profile\Payment\PaymentRepository;
use App\Model\User\Entity\Profile\Requisite\RequisiteRepository;
use App\Model\User\Service\Profile\Payment\Transaction\NumberGenerator;
use Doctrine\DBAL\Exception;
use Money\Currency;
use Money\Money;

/**
 * Class Handler
 * @package App\Model\User\UseCase\Profile\Payment\Transaction\Deposit
 */
class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var RequisiteRepository
     */
    private $requisiteRepository;

    /**
     * @var NumberGenerator
     */
    private $numberGenerator;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param PaymentRepository $paymentRepository
     * @param RequisiteRepository $requisiteRepository
     * @param NumberGenerator $numberGenerator
     */
    public function __construct(
        Flusher $flusher,
        PaymentRepository $paymentRepository,
        RequisiteRepository $requisiteRepository,
        NumberGenerator $numberGenerator
    ) {
        $this->flusher = $flusher;
        $this->paymentRepository = $paymentRepository;
        $this->requisiteRepository = $requisiteRepository;
        $this->numberGenerator = $numberGenerator;
    }

    /**
     * @param Command $command
     * @throws Exception
     */
    public function handle(Command $command): void {
        $payment = $this->paymentRepository->get(new Id($command->paymentId));
        $requisite = $this->requisiteRepository->get(new \App\Model\User\Entity\Profile\Requisite\Id($command->requisiteId));

        $numberGenerator = $this->numberGenerator->next();

        $payment->deposit(
            new Money($command->money, new Currency('RUB')),
            $numberGenerator,
            $requisite
        );

        $this->flusher->flush();
    }

}