<?php
declare(strict_types=1);

namespace App\Controller\Procedure\Lot;

use App\Model\User\Entity\Profile\Role\Permission;
use App\Model\Work\Procedure\Entity\Lot\Lot;
use App\Model\Work\Procedure\Entity\Lot\Status;
use App\Model\Work\Procedure\UseCase\Lot\Open\Edit\Command;
use App\Model\Work\Procedure\UseCase\Lot\Open\Edit\Form;
use App\Model\Work\Procedure\UseCase\Lot\Open\Edit\Handler;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use App\Security\Voter\LotAccess;
use App\Model\Work\Procedure\UseCase\Lot\Document\Upload\{
    Command as CommandUpload,
    Form as FormUpload,
    File as FileUpload,
    Handler as HandlerUpload
};
use App\Model\Work\Procedure\UseCase\Lot\Document\Delete\{
    Command as CommandDelete,
    Handler as HandlerDelete
};
use App\Model\Work\Procedure\UseCase\Lot\Document\Sign\{
    Command as CommandSign,
    Handler as HandlerSign
};
use App\ReadModel\Procedure\Lot\Document\DocumentFetcher;
use App\ReadModel\Procedure\Lot\Document\Filter\Filter;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Services\Uploader\FileUploader;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexController extends AbstractController
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

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * ProcedureController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param ProfileFetcher $profileFetcher
     */
    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger,
        ProfileFetcher $profileFetcher
    )
    {
        $this->profileFetcher = $profileFetcher;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * Список всех заявок в лоте | для организатора
     * @param Request $request
     * @param string $lotId
     * @param LotFetcher $lotFetcher
     * @param BidFetcher $bidFetcher
     * @return Response
     * @throws Exception
     * @Route("/lot/{lotId}/bids", name="lot.bids")
     */
    public function index(Request $request, string $lotId, LotFetcher $lotFetcher, BidFetcher $bidFetcher): Response
    {
        $lot = $lotFetcher->findDetail($lotId);

        if (!$this->isGranted('ROLE_MODERATOR')){
            $profile = $this->profileFetcher->findDetailByUserId($this->getUser()->getId());
            $this->denyAccessUnlessGranted(Permission::BIDS_FOR_LOT, [
                'profile' => $profile,
                'lot' => $lot
            ]);


            if ($profile->isOrganizer()) {
                $filter = \App\ReadModel\Procedure\Bid\Filter\Filter::organizerForLot($lotId);
            } elseif ($profile->isParticipant()) {
                $filter = \App\ReadModel\Procedure\Bid\Filter\Filter::participantForLot($lotId, $profile->id);
            }
        }else{
            $filter = \App\ReadModel\Procedure\Bid\Filter\Filter::forLot($lotId);
        }


        $pagination = $bidFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/procedures/lot/bids.html.twig', [
            'bids' => $pagination,
            'lot' => $lot
        ]);
    }


    /**
     * @param Request $request
     * @param string $procedureId
     * @param ProcedureFetcher $procedureFetcher
     * @param \App\Model\Work\Procedure\UseCase\Lot\Create\Handler $handler
     * @param RequisiteFetcher $requisiteFetcher
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @Route("/procedure/{procedureId}/lot/add", name="procedure.lot.add")
     */
    public function addLot(Request $request,
                           string $procedureId,
                           ProcedureFetcher $procedureFetcher,
                           \App\Model\Work\Procedure\UseCase\Lot\Create\Handler $handler,
                           RequisiteFetcher $requisiteFetcher,
                           ProfileFetcher $profileFetcher
    ): Response
    {
        $userId = $this->getUser()->getId();
      /*  $profile = $profileFetcher->findDetailByUserId($userId = $this->getUser()->getId());
        if (!$requisite = $requisiteFetcher->existsActiveRequisiteForPaymentId($profile->payment_id)) {
            $this->addFlash('error', 'Необходимо добавить реквизиты.');
            return $this->redirectToRoute('procedure.show', ['procedureId' => $procedureId]);
        }*/

        $command = new \App\Model\Work\Procedure\UseCase\Lot\Create\Command($procedureId, $request->getClientIp());
        $form = $this->createForm(\App\Model\Work\Procedure\UseCase\Lot\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Lot has been successfully added.', [], 'exceptions'));
                return $this->redirectToRoute('procedure.lots.list', ['procedure_id' => $procedureId]);
            } catch (\DomainException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error', $this->translator->trans($exception->getMessage(), [], 'exceptions'));
            }
        }


        $getIdNumberByProcedure = $procedureFetcher->findIdNumberByProcedure($procedureId);

        return $this->render('app/procedures/lot/create/auction.html.twig', [
            'form' => $form->createView(),
            'procedure_id' => $procedureId,
            'procedure_id_number' => $getIdNumberByProcedure->id_number
        ]);
    }

    /**
     * @param Request $request
     * @param string $lotId
     * @param FileUploader $fileUploader
     * @param LotFetcher $lotFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @param CommandUpload $handler
     * @return Response
     * @Route("/lot/{lotId}/upload", name="lot.upload.file")
     */
    public function upload(Request $request,
                           string $lotId,
                           FileUploader $fileUploader,
                           LotFetcher $lotFetcher,
                           ProcedureFetcher $procedureFetcher,
                           HandlerUpload $handler
    ): Response
    {
        $command = new CommandUpload($lotId, $request->getClientIp(), $this->getUser()->getId());
        $form = $this->createForm(FormUpload::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploaded = $fileUploader->upload($form->get('file')->getData(), true);
            $file = new FileUpload(
                $uploaded->getPath(),
                $uploaded->getName(),
                $uploaded->getSize(),
                $uploaded->getRealName(),
                $uploaded->getHash()
            );

            $command->file = $file;

            try {
                $handler->handle($command);
                $this->addFlash('success', 'Файл успешно загружен');
                return $this->redirectToRoute('lot.show', ['lotId' => $lotId]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }

        }
        $lot = $lotFetcher->findDetail($lotId);

        $procedure = $procedureFetcher->findIdNumberByProcedure($lot->procedure_id);
        return $this->render('app/procedures/lot/upload_file.html.twig', [
            'form' => $form->createView(),
            'lot' => $lot,
            'procedure' => $procedure
        ]);
    }


    /**
     * @param Request $request
     * @param string $lotId
     * @param string $documentId
     * @param HandlerDelete $handler
     * @return Response
     * @Route("/lot/{lotId}/document/{documentId}/delete", name="lot.document.delete")
     */
    public function documentDelete(Request $request, string $lotId, string $documentId, HandlerDelete $handler): Response
    {
        try {
            $handler->handle(new CommandDelete($lotId, $documentId));
            $this->addFlash('success', $this->translator->trans('Файл успешно удален', [], 'exceptions'));
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }
        return $this->redirectToRoute('lot.show', ['lot_id' => $lotId]);
    }


    /**
     * @param Request $request
     * @param HandlerSign $handler
     * @return Response
     * @Route("/lot/sign-uploaded-file", name="lot.sign")
     */
    public function signUploadedFile(Request $request, HandlerSign $handler): Response
    {

        $command = new CommandSign(
            $request->request->get('fileId'),
            $request->request->get('sign'),
            $request->getClientIp()
        );

        try {
            $handler->handle($command);
            return new Response('sign added successfully');
        } catch (\DomainException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param string $lotId
     * @param LotFetcher $lotFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @param DocumentFetcher $documentFetcher
     * @param ProfileFetcher $profileFetcher
     * @param \App\Model\Work\Procedure\UseCase\Lot\Open\PaymentWinnerConfirm\Handler $handler
     * @return Response
     * @Route("/lot/{lotId}", name="lot.show")
     */
    public function show(Request $request,
                         string $lotId,
                         LotFetcher $lotFetcher,
                         ProcedureFetcher $procedureFetcher,
                         DocumentFetcher $documentFetcher,
                         ProfileFetcher $profileFetcher,
                         \App\Model\Work\Procedure\UseCase\Lot\Open\PaymentWinnerConfirm\Handler $handler
    ): Response
    {
        $lot = $lotFetcher->findDetail($lotId);

        return $this->redirectToRoute('procedure.show', ['procedureId' => $lot->procedure_id]);

        $documents = $documentFetcher->all(
            Filter::fromLot($lotId),
            $request->query->getInt('page', 1),
            10
        );

        $profile = $profileFetcher->findDetailByUserId($userId = $this->getUser()->getId());


        $form = $this->createForm(\App\Model\Work\Procedure\UseCase\Lot\Open\PaymentWinnerConfirm\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Lot\Open\PaymentWinnerConfirm\Command($lotId, $lot->procedure_id));

        $form->handleRequest($request);

        try {
            if ($form->get('approved')->isClicked()) {
                $handler->handleConfirm($command);
                $this->addFlash('success', $this->translator->trans('Вы успешно подтверидтили оплату имущество победителем', [], 'exceptions'));
                return $this->redirectToRoute('lot.show', ['lotId' => $lotId]);
            }

            if ($form->get('reject')->isClicked()) {
                $handler->handleAnnulled($command);
                $this->addFlash('success', $this->translator->trans('Успешно', [], 'exceptions'));
                return $this->redirectToRoute('lot.show', ['lotId' => $lotId]);
            }
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }


        $procedure = $procedureFetcher->findIdNumberByProcedure($lot->procedure_id);
        $bids = [];
        return $this->render('app/procedures/lot/show.html.twig', [
            'lot' => $lot,
            'procedure' => $procedure,
            'certificate_thumbprint' => $profile->certificate_thumbprint ?? '',
            'documents' => $documents,
            'bids' => $bids,
            'form' => $form->createView()

        ]);
    }

    /**
     * @param Request $request
     * @param string $lot_id
     * @param LotFetcher $lotFetcher
     * @param Handler $handler
     * @return Response
     * @throws \Exception
     * @Route("/lot/{lot_id}/edit", name="lot.edit")
     */
    public function edit(Request $request, string $lot_id, LotFetcher $lotFetcher, Handler $handler): Response
    {
        $command = Command::edit(
            $this->getUser()->getId(),
            $lot = $lotFetcher->findDetail($lot_id),
            $request->getClientIp()
        );
        $userId = $this->getUser()->getId();
        $form = $this->createForm(Form::class, $command, ['user_id' => $userId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Lot changed successfully.', [], 'exceptions'));
                return $this->redirectToRoute('lot.show', ['lotId' => $lot_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/lot/edit.html.twig', [
            'form' => $form->createView(),
            'lot' => $lot
        ]);
    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $procedure_number
     * @param LotFetcher $lotFetcher
     * @return Response
     * @Route("/procedure/{procedure_id}/lots", name="procedure.lots.list")
     */
    public function showInProcedure(Request $request, string $procedure_id, LotFetcher $lotFetcher): Response
    {
        $lots = $lotFetcher->getAllInProcedure(
            $procedure_id,
            self::PER_PAGE,
            $request->query->getInt('page', 1),
            $request->get('sort'),
            $request->get('direction')
        );

        return $this->render('app/procedures/lot/list.html.twig', [
                'lots' => $lots,
                'procedureId' => $procedure_id,
                'procedureNumber' => $lotFetcher->getProcedureNumber($procedure_id) ?? ''
            ]);
    }
}
