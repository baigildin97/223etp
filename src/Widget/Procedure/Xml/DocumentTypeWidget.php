<?php
declare(strict_types=1);
namespace App\Widget\Procedure\Xml;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DocumentTypeWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('procedureXmlDocumentType', [$this, 'type'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function type(Environment $twig, string $type): string {
        return $twig->render('widget/procedure/xml/document_type.html.twig',[
            'type' => $type
        ]);
    }
}