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
 * Class DateApplicationSubmission
 * @package App\Widget\Admin\User
 */
class DateApplicationSubmission  extends AbstractExtension
{
    /**
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;

    /**
     * DateApplicationSubmission constructor.
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
            new TwigFunction('dateApplicationSubmission', [$this, 'dateApplicationSubmission'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    /**
     * @param Environment $twig
     * @param string|null $profile_id
     * @return string
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function dateApplicationSubmission(Environment $twig, ?string $profile_id){

        $date = $this->xmlDocumentFetcher->dateApplicationSubmission($profile_id);

        return $twig->render('widget/admin/user/date_application_submission.html.twig',[
            'date' => $date
        ]);

    }

}