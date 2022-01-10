<?php
declare(strict_types=1);
namespace App\Widget\Profile\XmlDocument\History;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModeratorRenderNameWidget extends AbstractExtension
{

    public function getFunctions(): array {
        return [
            new TwigFunction('profileXmlModeratorRenderName', [$this, 'action'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function action(Environment $twig, $data): string {
        return $twig->render('widget/profile/xml_document/history/moderator_name.html.twig',[
            'data' => $data,
        ]);
    }
}