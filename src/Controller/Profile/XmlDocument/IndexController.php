<?php
declare(strict_types=1);
namespace App\Controller\Profile\XmlDocument;

use App\Model\User\Entity\Profile\Document\FileType;
use App\Model\User\Service\Profile\File\FileHelper;
use App\Model\User\UseCase\Profile\Accredation\Moderator\Action\Handler;
use App\Model\User\UseCase\Profile\Accredation\Moderator\Action\Command;
use App\Model\User\UseCase\Profile\Accredation\Moderator\Action\Form;
use App\ReadModel\Profile\History\DetailView;
use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\Profile\XmlDocument\Filter\Filter;
use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use App\Security\Voter\ProfileAccess;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexController extends AbstractController
{
    private const PER_PAGE = 25;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * XmlDocumentController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @param ProfileFetcher $profileFetcher
     */
    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        XmlDocumentFetcher $xmlDocumentFetcher,
        ProfileFetcher $profileFetcher
    ){
        $this->logger = $logger;
        $this->translator = $translator;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
        $this->profileFetcher = $profileFetcher;
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @return Response
     * @Route("/profile/{profile_id}/xml-documents", name="profile.xml_documents")
     */
    public function index(Request $request, string $profile_id): Response{
        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->find($profile_id)
        );

        $filter = Filter::fromProfile($profile->id);

        $xmlDocuments = $this->xmlDocumentFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/profile/xml_document/index.html.twig', [
            'xml_documents' => $xmlDocuments,
            'profile' => $profile
        ]);
    }

    /**
     * @param string $profile_id
     * @param string $xml_document_id
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @param SerializerInterface $serializer
     * @return Response
     * @Route("/profile/{profile_id}/xml-document/{xml_document_id}/show", name="profile.xml_document.show")
     */
    public function show(
        string $profile_id,
        string $xml_document_id,
        XmlDocumentFetcher $xmlDocumentFetcher,
        SerializerInterface $serializer
    ): Response {
        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->find($profile_id)
        );

        $findXmlFile = $xmlDocumentFetcher->findDetailXmlFile($xml_document_id);
        if($findXmlFile === null){
            throw new \DomainException('Page Not Found');
        }


        $xmlFile = $serializer->deserialize($findXmlFile->file, DetailView::class, 'xml');

        $files = null;
        $filesCount = null;
        if (is_array($xmlFile->documents)){
            $filesCount = FileHelper::getFilesCount($xmlFile->documents, $profile, true);
            $files = FileHelper::rearrangeByTypeArray(
                $xmlFile->documents,
                $xmlFile->generalInformation['incorporatedFormOrigin']
            );
        }



        return $this->render('app/profile/xml_document/show.html.twig', [
            'xml' => $xmlFile,
            'profile' => $profile,
            'xml_document' => $findXmlFile,
            'files' => $files,
            'filesCount' => $filesCount,
            'typesNames' =>  FileType::getFileCategories($profile),
        ]);
    }
}
