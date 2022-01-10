<?php
declare(strict_types=1);
namespace App\Widget\Profile;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IncorporationFormWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('incorporation_form_profile', [$this, 'incorporationForm'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function incorporationForm(Environment $twig, ?string $incorporationForm): string {
        return $twig->render('widget/profile/incorporation_form.html.twig',[
            'incorporationForm' => $incorporationForm
        ]);
    }
}