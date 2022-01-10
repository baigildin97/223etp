<?php
declare(strict_types=1);
namespace App\Controller\Profile\XmlDocument;


use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\Profile\XmlDocument\History\Filter\Filter;
use App\ReadModel\Profile\XmlDocument\History\HistoryFetcher;
use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    private const PER_PAGE = 25;

    private $historyFetcher;

    private $profileFetcher;

    private $xmlDocumentFetcher;

    public function __construct(
        HistoryFetcher $historyFetcher,
        ProfileFetcher $profileFetcher,
        XmlDocumentFetcher $xmlDocumentFetcher
    ){
        $this->historyFetcher = $historyFetcher;
        $this->profileFetcher = $profileFetcher;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
    }

    /**
     * @Route("profile/{profile_id}/xml-document/{profile_xml_document_id}/history", name="profile_xml_document.history")
     * @param Request $request
     * @param string $profile_id
     * @param string $profile_xml_document_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, string $profile_id, string $profile_xml_document_id): Response {
        $profile = $this->profileFetcher->find($profile_id);
        $xmlDocument = $this->xmlDocumentFetcher->findDetailXmlFile($profile_xml_document_id);

        if ($this->isGranted('ROLE_MODERATOR')){
            $filter = Filter::fromXmlDocument($profile_xml_document_id);
        } else {
            $filter = Filter::fromXmlDocumentParticipant($profile_xml_document_id, $profile->user_email);
        }

        $pagination = $this->historyFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/profile/xml_document/history/index.html.twig', [
            'pagination' => $pagination,
            'profile' => $profile,
            'xml_document' => $xmlDocument
        ]);
    }

}