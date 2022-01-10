<?php
declare(strict_types=1);
namespace App\Widget\Admin\User;

use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use Doctrine\DBAL\Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class StatusWidget
 * @package App\Widget\Admin\User
 */
class StatusWidget extends AbstractExtension
{
    /**
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;

    /**
     * StatusWidget constructor.
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     */
    public function __construct(XmlDocumentFetcher $xmlDocumentFetcher)
    {
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array {
        return [
            new TwigFunction('user_status', [$this, 'status'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    /**
     * @param Environment $twig
     * @param string $status
     * @param string|null $profile_status
     * @param string|null $profile_id
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function status(Environment $twig, string $status, ?string $profile_status = null, ?string $profile_id = null): string {
        $countXmlDocuments = $this->xmlDocumentFetcher->countXmlDocuments($profile_id);
	
        return $twig->render('widget/admin/user/status.html.twig',[
            'status' => $status,
            'profile_status' => $profile_status,
            'count_xml_documents' => $countXmlDocuments
        ]);
    }

}