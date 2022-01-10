<?php
declare(strict_types=1);

namespace App\Controller\Admin\Moderator;

use App\Model\Work\Procedure\Entity\Status;
use App\Model\Work\Procedure\UseCase\Moderator\Processing\Confirm\Form;
use App\Model\Work\Procedure\UseCase\Moderator\Processing\Start\Command;
use App\Model\Work\Procedure\UseCase\Moderator\Processing\Start\Handler;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Procedure\XmlDocument\Filter\Filter;
use App\ReadModel\Procedure\XmlDocument\XmlDocumentFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ProcedureController
 * @package App\Controller\Admin\Moderator
 * @IsGranted("ROLE_MODERATOR")
 */
class ProcedureController extends AbstractController
{
    private const PER_PAGE = 10;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $xmlDocumentFetcher;

    /**
     * @var ProcedureFetcher
     */
    private $procedureFetcher;

    /**
     * ProcedureController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @param ProcedureFetcher $procedureFetcher
     */
    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger,
        XmlDocumentFetcher $xmlDocumentFetcher,
        ProcedureFetcher $procedureFetcher
    ){
        $this->translator = $translator;
        $this->logger = $logger;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
        $this->procedureFetcher = $procedureFetcher;
    }

    /**
     * Список извещений ожидающие модерацию
     * @param Request $request
     * @return Response
     * @Route("/moderate/procedures", name="moderate.procedures")
     */
    public function index(Request $request): Response
    {
        $filter = Filter::fromStatusTactic(\App\Model\Work\Procedure\Entity\XmlDocument\Status::moderate()->getName() ,\App\Model\Work\Procedure\Entity\XmlDocument\StatusTactic::published()->getName());

        $pagination = $this->xmlDocumentFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/admin/moderator/procedures/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Список извещений которые находятся у модератора в работе
     * @param Request $request
     * @param string $moderator_id
     * @return Response
     * @Route("/moderate/procedures/processing/{moderator_id}", name="moderate.procedures.processing")
     */
    public function moderatorProcessing(Request $request, string $moderator_id): Response {
        $filter = Filter::fromModeratorProcessing(
            $moderator_id,
            \App\Model\Work\Procedure\Entity\XmlDocument\StatusTactic::processing()->getName()
        );

        $pagination = $this->xmlDocumentFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/admin/moderator/procedures/processing/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $xml_document_id
     * @param Handler $handler
     * @return Response
     * @Route("/moderate/procedure/{procedure_id}/processing/{xml_document_id}/start", name="moderate.procedure.processing.start")
     */
    public function startProcedureModerate(Request $request, string $procedure_id, string $xml_document_id, Handler $handler): Response {
        $command = new Command(
            $this->getUser()->getId(),
            $xml_document_id,
            $request->getClientIp()
        );
        try {
            $handler->handle($command);
        } catch (\DomainException $e){
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('procedure.notification.show', [
            'notification_id' =>$xml_document_id,
            'procedure_id' => $procedure_id
        ]);
    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $xml_document_id
     * @param \App\Model\Work\Procedure\UseCase\Moderator\Processing\Returned\Handler $handler
     * @return Response
     * @Route("/moderate/procedure/{procedure_id}/processing/{xml_document_id}/return", name="moderate.procedure.processing.return")
     */
    public function returnProcedureProcessing(Request $request, string $procedure_id, string $xml_document_id, \App\Model\Work\Procedure\UseCase\Moderator\Processing\Returned\Handler $handler): Response {
        $command = new \App\Model\Work\Procedure\UseCase\Moderator\Processing\Returned\Command(
            $this->getUser()->getId(),
            $xml_document_id,
            $request->getClientIp()
        );
        try {
            $handler->handle($command);
        } catch (\DomainException $e){
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('procedure.notification.show', [
            'notification_id' =>$xml_document_id,
            'procedure_id' => $procedure_id
        ]);
    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $xml_document_id
     * @return Response
     * @Route("/moderate/procedure/{procedure_id}/processing/{xml_document_id}/confirm", name="moderate.procedure.processing.confirm")
     */
    public function processingConfirm(Request $request, string $procedure_id, string $xml_document_id, \App\Model\Work\Procedure\UseCase\Moderator\Processing\Confirm\Handler $handler): Response {
        $procedure = $this->procedureFetcher->findDetail($procedure_id);
        $xmlDocument = $this->xmlDocumentFetcher->findDetailXmlFile($xml_document_id);
        $form = $this->createForm(Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Moderator\Processing\Confirm\Command(
                $xml_document_id,
                $this->getUser()->getId(),
                $request->getClientIp()
            )
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            try {
                if($form->get('approved')->isClicked()){
                    $handler->approveHandle($command);
                    $this->addFlash('success',  'Извещение одобрено');
                }
                if($form->get('reject')->isClicked()){
                    $handler->rejectHandle($command);
                    $this->addFlash('success',  'Извещение отклонено');
                }
                return $this->redirectToRoute('procedure.notification.show', ['procedure_id' => $procedure_id, 'notification_id' => $xml_document_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/notifications/confirm.html.twig', [
            'form' => $form->createView(),
            'procedure' => $procedure,
            'notification' => $xmlDocument
        ]);
    }

}
