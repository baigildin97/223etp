<?php
declare(strict_types=1);

namespace App\Widget;

use App\ReadModel\Profile\Payment\PaymentFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class NegativeBalanceWidget
 * @package App\Widget
 */
class NegativeBalanceWidget extends AbstractExtension
{
    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var PaymentFetcher
     */
    private $paymentFetcher;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;


    public function __construct(ProfileFetcher $profileFetcher, PaymentFetcher $paymentFetcher, TokenStorageInterface $tokenStorage){
        $this->profileFetcher = $profileFetcher;
        $this->paymentFetcher = $paymentFetcher;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('negative_balance', [$this, 'run'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function run(Environment $twig): string
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return '';
        }

        if (!($userIdentity = $token->getUser()) instanceof UserIdentity) {
            return '';
        }

        $payment = null;
        $profile = $this->profileFetcher->findDetailByUserId($userIdentity->getId());
        if (isset($profile->payment_id)){
            $payment = $this->paymentFetcher->findDetail($profile->payment_id);
        }


        return $twig->render('widget/negative_balance.html.twig', [
            'payment' => $payment
        ]);
    }
}