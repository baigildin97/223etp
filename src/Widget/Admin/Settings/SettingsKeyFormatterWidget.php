<?php
declare(strict_types=1);
namespace App\Widget\Admin\Settings;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsKeyFormatterWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('settings_key', [$this, 'key'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function key(Environment $twig, string $current, string $token): string {
        return $twig->render('widget/profile/actions.html.twig',[
            'current' => $current,
            'token' => $token
        ]);
    }
}