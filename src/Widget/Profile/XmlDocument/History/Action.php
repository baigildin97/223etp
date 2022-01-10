<?php
declare(strict_types=1);
namespace App\Widget\Profile\XmlDocument\History;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Action extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('actionHistoryProfileXmlDocument', [$this, 'action'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function action(Environment $twig, string $action): string {
        return $twig->render('widget/profile/xml_document/history/actions.html.twig',[
            'action' => $action,
        ]);
    }
}