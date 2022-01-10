<?php
declare(strict_types=1);
namespace App\Widget;


use App\ReadModel\Profile\ProfileFetcher;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RightMenuWidget extends AbstractExtension
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
            new TwigFunction('right_menu', [$this, 'right_menu'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function right_menu(Environment $twig){

        if (null === $token = $this->tokenStorage->getToken()) {
            return '';
        }

        if (!($userIdentity = $token->getUser()) instanceof UserIdentity) {
            return '';
        }


        if ($profile = $this->profileFetcher->findDetailByUserId($userIdentity->getId())){
            $avatar = mb_strtoupper(mb_substr($profile->repr_passport_last_name,0,1,"UTF-8").mb_substr($profile->repr_passport_first_name,0,1,"UTF-8"));
        }else{
            $avatar = 'MR';
        }

        return $twig->render('widget/right_menu.html.twig', [
                'profile' => $profile,
                'user_id' => $userIdentity->getId(),
                'avatar' => $avatar
            ]
        );
    }
}