<?php
declare(strict_types=1);
namespace App\Widget\Profile;

use App\ReadModel\Profile\ProfileFetcher;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkChangeEds extends AbstractExtension
{

    private $tokenStorage;
    private $profileFetcher;

    public function __construct(ProfileFetcher $profileFetcher, TokenStorageInterface $tokenStorage){
        $this->tokenStorage = $tokenStorage;
        $this->profileFetcher = $profileFetcher;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('link_change_eds', [$this, 'incorporationForm'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function incorporationForm(Environment $twig, string $incorporationForm, string $status, string $profile_id): string
    {

        return $twig->render('widget/profile/link_change_eds.html.twig', [
            'incorporationForm' => $incorporationForm,
            'status' => $status,
            'profile_id' => $profile_id
        ]);
    }
}