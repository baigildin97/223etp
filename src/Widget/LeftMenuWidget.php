<?php
declare(strict_types=1);
namespace App\Widget;


use App\ReadModel\Profile\ProfileFetcher;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LeftMenuWidget extends AbstractExtension
{
    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(ProfileFetcher $profileFetcher, TokenStorageInterface $tokenStorage){
        $this->profileFetcher = $profileFetcher;
        $this->tokenStorage = $tokenStorage;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('left_menu', [$this, 'left_menu'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function left_menu(Environment $twig){

        if (null === $token = $this->tokenStorage->getToken()) {
            return '';
        }

        if (!($userIdentity = $token->getUser()) instanceof UserIdentity) {
            return '';
        }


        $profile = $this->profileFetcher->findDetailByUserId($userIdentity->getId());

        return $twig->render('widget/left_menu.html.twig', [
                'profile' => $profile,
                'user_id' => $userIdentity->getId()
            ]
        );
    }
}