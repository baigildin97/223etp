<?php
declare(strict_types=1);
namespace App\Widget\Profile;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CurrentWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('current_profile', [$this, 'current', 'token'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function current(Environment $twig, string $current, string $token): string {
        return $twig->render('widget/profile/current.html.twig',[
            'current' => $current,
            'token' => $token
        ]);
    }
}