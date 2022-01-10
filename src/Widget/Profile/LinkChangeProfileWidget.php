<?php
declare(strict_types=1);

namespace App\Widget\Profile;


use App\ReadModel\Profile\ProfileFetcher;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkChangeProfileWidget extends AbstractExtension
{
    private $profileFetcher;

    public function __construct(ProfileFetcher $profileFetcher)
    {
        $this->profileFetcher = $profileFetcher;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('link_change_profile', [$this, 'incorporationForm'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function incorporationForm(Environment $twig, string $incorporationForm, string $status, string $profile_id): string
    {

        return $twig->render('widget/profile/link_change_profile.html.twig', [
            'incorporationForm' => $incorporationForm,
            'status' => $status,
            'profile_id' => $profile_id
        ]);
    }
}