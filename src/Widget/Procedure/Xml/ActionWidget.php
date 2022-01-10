<?php
namespace App\Widget\Procedure\Xml;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ActionWidget
 * @package App\Widget\Procedure\Xml
 */
class ActionWidget extends AbstractExtension
{
    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array {
        return [
            new TwigFunction('actionHistoryProcedureXmlDocument', [$this, 'action'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    /**
     * @param Environment $twig
     * @param string $action
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function action(Environment $twig, string $action): string {
        return $twig->render('widget/procedure/xml/actions.html.twig',[
            'action' => $action,
        ]);
    }
}