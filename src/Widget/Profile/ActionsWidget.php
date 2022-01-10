<?php
declare(strict_types=1);
namespace App\Widget\Profile;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ActionsWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('actions_profile', [$this, 'current', 'token'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function current(Environment $twig, string $current, string $token): string {
        return $twig->render('widget/profile/actions.html.twig',[
            'current' => $current,
            'token' => $token
        ]);
    }
}