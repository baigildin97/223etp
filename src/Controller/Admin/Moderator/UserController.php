<?php
declare(strict_types=1);
namespace App\Controller\Admin\Moderator;

use App\Model\User\Entity\Profile\XmlDocument\Status;
use App\Model\User\Entity\Profile\XmlDocument\StatusTactic;
use App\Model\User\UseCase\Profile\Accredation\Moderator\Processing;
use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Model\User\UseCase\Profile\Accredation\Moderator\Action;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class UserController
 * @package App\Controller\Admin\Moderator
 * @IsGranted("ROLE_MODERATOR")
 */
class UserController extends AbstractController
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
     * @var ProfileFetcher
     */
    private $profileFetcher;

    private $xmlDocumentFetcher;

    /**
     * UserController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param ProfileFetcher $profileFetcher
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     */
    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        ProfileFetcher $profileFetcher,
        XmlDocumentFetcher $xmlDocumentFetcher
    ){
        $this->logger = $logger;
        $this->translator = $translator;
        $this->profileFetcher = $profileFetcher;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
    }

    /**
     * Список документов профилей ожидающие модерацию
     * @param Request $request
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @return Response
     * @Route("/moderate/users", name="moderate.users")
     */
    public function index(Request $request, XmlDocumentFetcher $xmlDocumentFetcher): Response
    {
        $filter = \App\ReadModel\Profile\XmlDocument\Filter\Filter::fromModerateList();
        $form = $this->createForm(\App\ReadModel\Profile\XmlDocument\Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $xmlDocumentFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->get('direction')
        );

    //    dd($pagination);

        return $this->render('app/admin/moderator/users/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * Показываем модератору его задачи по модерации профилей
     * @param Request $request
     * @param string $moderator_id
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @return Response
     * @Route("/moderate/users/processing/{moderator_id}", name="moderate.users.processing")
     */
    public function moderatorProcessing(Request $request, string $moderator_id, XmlDocumentFetcher $xmlDocumentFetcher){
        $filter = \App\ReadModel\Profile\XmlDocument\Filter\Filter::fromModeratorProcessing(
            $moderator_id,
            StatusTactic::processing()->getName()
        );

        $form = $this->createForm(\App\ReadModel\Profile\XmlDocument\Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $xmlDocumentFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/admin/moderator/users/processing/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
            'clear_url' => $this->generateUrl('moderate.users.processing', ['moderator_id' => $moderator_id])
        ]);
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param string $xml_document_id
     * @param Processing\Start\Handler $handler
     * @return Response
     * @Route("/moderate/processing/profile/{profile_id}/document/{xml_document_id}/start", name="moderate.processing.profile.start")
     */
    public function startProfileProcessing(
        Request $request,
        string $profile_id,
        string $xml_document_id,
        Processing\Start\Handler $handler
    ): Response {
        $command = new Processing\Start\Command($xml_document_id, $this->getUser()->getId(),$request->getClientIp());
        try {
            $handler->handle($command);
        }catch (\DomainException $e){
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('profile.xml_document.show', [
            'profile_id' => $profile_id,
            'xml_document_id' =>$xml_document_id
        ]);
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param string $xml_document_id
     * @param Processing\Returned\Handler $handler
     * @return Response
     * @Route("/moderate/processing/profile/{profile_id}/document/{xml_document_id}/return", name="moderate.processing.profile.return")
     */
    public function returnProfileProcessing(
        Request $request,
        string $profile_id,
        string $xml_document_id,
        Processing\Returned\Handler $handler
    ): Response {
        $command = new Processing\Returned\Command(
            $xml_document_id,
            $this->getUser()->getId(),
            $request->getClientIp()
        );
        try {
            $handler->handle($command);
        }catch (\DomainException $e){
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('profile.xml_document.show', [
            'profile_id' => $profile_id,
            'xml_document_id' =>$xml_document_id
        ]);
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param string $xml_document_id
     * @param Action\Handler $handler
     * @return Response
     * @throws \Exception
     * @Route("/moderate/processing/profile/{profile_id}/document/{xml_document_id}/confirm", name="moderate.processing.profile.confirm")
     */
    public function processingConfirm(
        Request $request,
        string $profile_id,
        string $xml_document_id,
        Action\Handler $handler
    ): Response {
        $clientProfile = $this->profileFetcher->find($profile_id);
        $xmlDocument = $this->xmlDocumentFetcher->findDetailXmlFile($xml_document_id);
        $form = $this->createForm(
            Action\Form::class,
            $command = new Action\Command($xml_document_id, $profile_id, $this->getUser()->getId(),$request->getClientIp())
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            try{
                if ($form->get('approved')->isClicked()){
                    $handler->handleApprove($command);
                    $this->addFlash('success',  'Заявление пользователя одобрено.');
                    return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
                }
                if ($form->get('reject')->isClicked()){
                    $handler->handleReject($command);
                    $this->addFlash('success',  'Заявление пользователя отклонено.');
                    return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
                }
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/admin/moderator/users/processing/confirm.html.twig', [
            'form' => $form->createView(),
            'profile' => $clientProfile,
            'xml_document' => $xmlDocument
        ]);
    }
}
