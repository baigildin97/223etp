<?php
declare(strict_types=1);
namespace App\Widget\Admin\Moderator\XmlDocument;


use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class XmlDocumentStatusWidget
 * @package App\Widget\Admin\Moderator\XmlDocument
 */
class XmlDocumentStatusWidget extends AbstractExtension
{
    /**
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;

    /**
     * XmlDocumentStatusWidget constructor.
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     */
    public function __construct(XmlDocumentFetcher $xmlDocumentFetcher){
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('XmlDocumentStatus', [$this, 'XmlDocumentStatus'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    /**
     * @param Environment $twig
     * @param string $profile_id
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function XmlDocumentStatus(Environment $twig, string $profile_id){
        $countXmlDocuments = $this->xmlDocumentFetcher->countXmlDocuments($profile_id);

        return $twig->render('widget/admin/moderator/xml_document/status.html.twig', [
                'count_xml_documents' => $countXmlDocuments
            ]
        );
    }
}