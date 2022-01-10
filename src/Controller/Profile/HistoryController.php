<?php
declare(strict_types=1);
namespace App\Controller\Profile;

use App\Model\User\Entity\Profile\Document\FileType;
use App\Model\User\Service\Profile\File\FileHelper;
use App\Model\User\UseCase\Profile\Accredation\Moderator\Action\Handler;
use App\Model\User\UseCase\Profile\Accredation\Moderator\Action\Command;
use App\Model\User\UseCase\Profile\Accredation\Moderator\Action\Form;
use App\ReadModel\Profile\History\DetailView;
use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\Profile\XmlDocument\Filter\Filter;
use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HistoryController extends AbstractController
{
    private const PER_PAGE = 10;

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

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, XmlDocumentFetcher $xmlDocumentFetcher, ProfileFetcher $profileFetcher){
        $this->logger = $logger;
        $this->translator = $translator;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
        $this->profileFetcher = $profileFetcher;
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @return Response
     * @Route("/profile/{profile_id}/history", name="profile.history")
     */
    public function index(Request $request, string $profile_id): Response{
        $profile = $this->profileFetcher->find($profile_id);
        $filter = Filter::fromProfile($profile->id);
        $profileHistory = $this->xmlDocumentFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/profile/history/index.html.twig', [
            'profile_history' => $profileHistory,
            'profile' => $profile
        ]);
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param string $historyId
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @param SerializerInterface $serializer
     * @return Response
     * @Route("/profile/{profile_id}/history/{historyId}/show", name="profile.history.show")
     */
    public function show(
        Request $request,
        string $profile_id,
        string $historyId,
        XmlDocumentFetcher $xmlDocumentFetcher,
        SerializerInterface $serializer,
        Handler $handler
    ): Response{

        $profile = $this->profileFetcher->find($profile_id);

        $findXmlFile = $xmlDocumentFetcher->findDetailXmlFile($historyId);
        if($findXmlFile === null){
            throw new \DomainException('Page Not Found');
        }

        $xmlFile = $serializer->deserialize($findXmlFile->file, DetailView::class, 'xml');

        $files = null;
        $filesCount = null;
        if (is_array($xmlFile->documents)){
            $filesCount = FileHelper::getFilesCount($xmlFile->documents, $profile);
            $files = FileHelper::rearrangeByTypeArray(
                $xmlFile->documents,
                $xmlFile->generalInformation['incorporatedFormOrigin']
            );
        }


        $form = $this->createForm(
            Form::class,
            $command = new Command($findXmlFile->id, $profile_id, $this->getUser()->getId())
        );

        $form->handleRequest($request);

        try{
            if ($form->get('approved')->isClicked()){
                $handler->handleApprove($command);
                $this->addFlash('success',  'Акрредитация успешно одобрена');
                return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
            }

            if ($form->get('reject')->isClicked()){
                $handler->handleReject($command);
                $this->addFlash('success',  'Акрредитация успешно отклонена');
                return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
            }
        }catch (\DomainException $e){
            $this->logger->error($e->getMessage(), ['exception'=>$e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
        }

        return $this->render('app/profile/history/show.html.twig', [
            'xml' => $xmlFile,
            'profile' => $profile,
            'created_at_xml_document' => $findXmlFile->created_at,
            'files' => $files,
            'filesCount' => $filesCount,
            'typesNames' =>  FileType::getFileCategories($profile),
            'form' => $form->createView()
        ]);
    }
}
