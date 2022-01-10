<?php
declare(strict_types=1);

namespace App\Widget\Profile;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Country extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('country', [$this, 'country'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function country(Environment $twig, string $country): string
    {
        return $twig->render('widget/profile/country.html.twig', [
            'country' => $country
        ]);
    }
}