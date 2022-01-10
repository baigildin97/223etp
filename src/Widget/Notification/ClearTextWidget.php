<?php
declare(strict_types=1);

namespace App\Widget\Notification;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ClearTextWidget extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('clearText', [$this, 'clearText'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function clearText(Environment $twig, string $text): string
    {
            $text = str_replace(['\r\n', '\r', '\n'], ' ', strip_tags($text));
        return $twig->render('widget/notification/clear_text.html.twig', [
            'text' => stripcslashes($text)

        ]);
    }


}