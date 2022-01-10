<?php
declare(strict_types=1);
namespace App\Controller\Procedure;

use App\Model\Work\Procedure\Entity\XmlDocument\Status;
use App\Model\Work\Procedure\Entity\XmlDocument\Type;
use App\Model\Work\Procedure\Services\Procedure\Notifications\XmlNotifyDetailView;
use App\Model\Work\Procedure\Services\Procedure\Notifications\XmlNotifyGenerator;
use App\Model\Work\Procedure\Services\Procedure\Sign\NotifyDetailView;
use App\Model\Work\Procedure\UseCase\Recall\Command;
use App\Model\Work\Procedure\UseCase\Recall\Handler;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Procedure\XmlDocument\Filter\Filter;
use App\ReadModel\Procedure\XmlDocument\XmlDocumentFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Security\Voter\NotificationAccess;
use App\Services\Hash\Streebog;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationController extends AbstractController
{
    private const PER_PAGE = 15;

    private $xmlDocumentFetcher;

    private $procedureFetcher;

    private $logger;

    private $translator;

    private $profileFetcher;

    public function __construct(
        XmlDocumentFetcher $xmlDocumentFetcher,
        ProcedureFetcher $procedureFetcher,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        ProfileFetcher $profileFetcher
    ){
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
        $this->procedureFetcher = $procedureFetcher;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->profileFetcher = $profileFetcher;
    }

    /**
     * @param string $procedure_id
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @return Response
     * @Route("/procedure/{procedure_id}/notifications", name="procedure.notifications")
     */
    public function index(string $procedure_id): Response{
        $procedure = $this->procedureFetcher->findDetail($procedure_id);

        $profile = $this->profileFetcher->findDetailByUserId($this->getUser()->getId());

        if (!$this->isGranted('ROLE_MODERATOR')){
            if ($profile->isOrganizer()){
                $filter = Filter::fromProcedure($procedure->id);
            }else{
                $filter = Filter::fromProcedureByStatus($procedure->id, Status::signed());
            }
        }else{
            $filter = Filter::fromProcedure($procedure->id);
        }


        $xmlDocuments = $this->xmlDocumentFetcher->all(
            $filter,
            1,
            self::PER_PAGE
        );

        return $this->render('app/procedures/notifications/index.html.twig',[
            'notifications' => $xmlDocuments,
            'procedure' => $procedure
        ]);
    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param ProcedureFetcher $procedureFetcher
     * @return Response
     * @Route("/procedure/{procedure_id}/notification/create", name="procedure.notification.create")
     */
    public function create(Request $request, string $procedure_id, ProcedureFetcher $procedureFetcher): Response {
        $form = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Notification\Create\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Notification\Create\Command()
        );

        $procedure = $procedureFetcher->findDetail($procedure_id);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $type = new Type($command->notificationType);
                return $this->redirectToRoute('procedure.notification.generate', ['procedure_id'=>$procedure_id, 'notification_type'=> $type->getName()]);
            }catch (\DomainException $exception){
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/procedures/notifications/switch.html.twig', [
            'form' => $form->createView(),
            'procedure' => $procedure
        ]);
    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $notification_type
     * @param ProcedureFetcher $procedureFetcher
     * @return Response
     * @Route("/procedure/{procedure_id}/notification/{notification_type}/generate", name="procedure.notification.generate")
     */
    public function generate(Request $request, string $procedure_id, string $notification_type, ProcedureFetcher $procedureFetcher): Response {
        $type = new Type($notification_type);
        if ($type->isNotifyPublish()){
            return $this->redirectToRoute('procedure.sign', ['procedureId'=>$procedure_id]);
        }

        $form = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Notification\Create\Generate\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Notification\Create\Generate\Command()
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            return $this->redirectToRoute('procedure.notification.sign', ['procedure_id'=>$procedure_id, 'notification_type'=>$notification_type, 'organizer_comment'=>$command->organizerComment]);
        }

        $procedure = $procedureFetcher->findDetail($procedure_id);

        return $this->render('app/procedures/lot/protocol/create.html.twig', [
            'procedure' => $procedure,
            'type' => $type->getName(),
            'typeName' => $type->getLocalizedName(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $notification_type
     * @param string $organizer_comment
     * @param SerializerInterface $serializer
     * @param ProcedureFetcher $procedureFetcher
     * @param Streebog $streebog
     * @param XmlNotifyGenerator $xmlNotifyGenerator
     * @param \App\Model\Work\Procedure\UseCase\Notification\Create\Sign\Handler $handler
     * @return Response
     * @Route("/procedure/{procedure_id}/notification/{notification_type}/sign/{organizer_comment}", name="procedure.notification.sign")
     */
    public function sign(
        Request $request,
        string $procedure_id,
        string $notification_type,
        string $organizer_comment,
        SerializerInterface $serializer,
        ProcedureFetcher $procedureFetcher,
        Streebog $streebog,
        XmlNotifyGenerator $xmlNotifyGenerator,
        \App\Model\Work\Procedure\UseCase\Notification\Create\Sign\Handler $handler
    ): Response {
        $procedure = $procedureFetcher->findDetail($procedure_id);

        $xml = $xmlNotifyGenerator->generate($procedure, new Type($notification_type), $organizer_comment);
        $hash = $streebog->getHash($xml);

        $signForm = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Notification\Create\Sign\Form::class,
            $commandSign = new \App\Model\Work\Procedure\UseCase\Notification\Create\Sign\Command(
                $procedure_id,
                $xml,
                $hash,
                $notification_type
            )
        );
        $signForm->handleRequest($request);

        if ($signForm->isSubmitted() && $signForm->isValid()){
            try {
                $handler->handle($commandSign);
                $this->addFlash('success',  'Извещение опубликовано.');
                return $this->redirectToRoute('procedure.notifications', ['procedure_id' => $procedure_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        $xmlD = $serializer->deserialize($xml, XmlNotifyDetailView::class,'xml');
        $notificationType = new Type($notification_type);

        return $this->render('app/procedures/notifications/sign.html.twig', [
            'procedure' => $procedure,
            'hash' => $hash,
            'form' => $signForm->createView(),
            'xml' => $xmlD,
            'notificationType' => $notificationType->getName(),
            'notificationName' => $notificationType->getLocalizedName()
        ]);
    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $notification_id
     * @param SerializerInterface $serializer
     * @param Streebog $streebog
     * @param \App\Model\Work\Procedure\UseCase\Notification\Sign\Handler $signHandler
     * @return Response
     * @Route("/procedure/{procedure_id}/notification/{notification_id}", name="procedure.notification.show")
     */
    public function show(
        Request $request,
        string $procedure_id,
        string $notification_id,
        SerializerInterface $serializer,
        Streebog $streebog,
        \App\Model\Work\Procedure\UseCase\Notification\Sign\Handler $signHandler
    ): Response {
        $procedure = $this->procedureFetcher->findDetail($procedure_id);
        $xmlDocument = $this->xmlDocumentFetcher->findDetailXmlFile($notification_id);

        $formSign = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Notification\Sign\Form::class,
            $commandSign = new \App\Model\Work\Procedure\UseCase\Notification\Sign\Command(
                $notification_id,
                $hash = $streebog->getHash($xmlDocument->file)
            )
        );

        $formSign->handleRequest($request);
        if ($formSign->isSubmitted() && $formSign->isValid()) {

            try {
                $signHandler->handle($commandSign);
                $this->addFlash('success', 'Процедура опубликована.');
                return $this->redirectToRoute('procedure.show', ['procedureId' => $procedure_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        $xml = $serializer->deserialize($xmlDocument->file,NotifyDetailView::class,'xml');



        return $this->render('app/procedures/notifications/show.html.twig', [
            'procedure' => $procedure,
            'xml' => $xml,
            'hash' => $hash,
            'notification' => $xmlDocument,
            'formSign' => $formSign->createView()
        ]);
    }


    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $notification_id
     * @param Handler $handler
     * @return Response
     * @Route("/procedure/{procedure_id}/notification/{notification_id}/recall", name="procedure.notification.recall")
     */
    public function recall(Request $request, string $procedure_id, string $notification_id, Handler $handler): Response{
        $command = new Command(
            $notification_id,
            $procedure_id,
            $request->getClientIp()
        );
        try {
            $xmlDocument = $this->xmlDocumentFetcher->findDetailXmlFile($notification_id);
            $handler->handle($command);
            $this->addFlash('success', 'Извещение на проведение торговой процедуры №'.$xmlDocument->number.' отозвано');
            return $this->redirectToRoute('procedure.show', ['procedureId' => $procedure_id]);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }
        return $this->redirectToRoute('procedure.notifications', ['procedure_id' => $procedure_id]);
    }


    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $notification_id
     * @param \App\Model\Work\Procedure\UseCase\Notification\Recall\Handler $handler
     * @return Response
     * @Route("/procedure/{procedure_id}/notification/{notification_id}/recall_published", name="procedure.notification.recall_published")
     */
    public function cancellingPublication(Request $request, string $procedure_id, string $notification_id, \App\Model\Work\Procedure\UseCase\Notification\Recall\Handler $handler): Response{
        $command = new \App\Model\Work\Procedure\UseCase\Notification\Recall\Command(
            $notification_id,
            $procedure_id,
            $request->getClientIp()
        );

        try {
            $xmlDocument = $this->xmlDocumentFetcher->findDetailXmlFile($notification_id);
            $handler->handle($command);
            $this->addFlash('success', 'Торговая процедура №'.$xmlDocument->number.' отменена');
            return $this->redirectToRoute('procedure.show', ['procedureId' => $procedure_id]);
        }catch (\DomainException $e){
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }
        return $this->redirectToRoute('procedure.notifications', ['procedure_id' => $procedure_id]);
    }

}