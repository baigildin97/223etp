<?php
declare(strict_types=1);
namespace App\Widget\Front;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthLinksWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('navbar', [$this, ''], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function navbar(Environment $twig): string {

        return $twig->render('widget/front/auth_links.html.twig', []);
    }
}