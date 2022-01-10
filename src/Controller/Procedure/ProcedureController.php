<?php
declare(strict_types=1);

namespace App\Controller\Procedure;

use App\Model\Admin\Entity\Settings\Key;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\Work\Procedure\Entity\Document\FileType;
use App\Model\Work\Procedure\Entity\Status;
use App\Model\Work\Procedure\Services\Procedure\File\FileHelper;
use App\Model\Work\Procedure\Services\Procedure\Sign\NotifyDetailView;
use App\Model\Work\Procedure\Services\Procedure\Sign\SignXmlGenerator;
use App\Model\Work\Procedure\UseCase\Files\Upload\File;
use App\Model\Work\Procedure\UseCase\Lot\Open\PaymentWinnerConfirm\Handler;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Procedure\Document\DocumentFetcher;
use App\ReadModel\Procedure\Filter\Filter;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Procedure\XmlDocument\XmlDocumentFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Security\Voter\ProcedureAccess;
use App\Services\Hash\Streebog;
use App\Services\Main\GlobalRoleAccessor;
use App\Services\Uploader\FileUploader;
use Doctrine\DBAL\Exception;
use Ekapusta\OAuth2Esia\Security\JWTSigner\TmpFile;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Model\Work\Procedure\UseCase\Files\Upload\Handler as HandlerUpload;
use App\Model\Work\Procedure\UseCase\Files\Upload\Command as CommandUpload;
use App\Model\Work\Procedure\UseCase\Files\Upload\Form as FormUpload;
use App\Model\Work\Procedure\UseCase\Files\Delete\Handler as DeleteHandler;
use App\Model\Work\Procedure\UseCase\Files\Delete\Command as DeleteCommand;
use App\Model\Work\Procedure\UseCase\Files\Sign\Handler as SignHandler;
use App\Model\Work\Procedure\UseCase\Files\Sign\Command as SignCommand;

/**
 * Class ProcedureController
 * @package App\Controller\Procedure
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

    /**
     * @var tmpfile()
     */
    private $tempfile;

    /**
     * @var GlobalRoleAccessor
     */
    private $globalRoleAccessor;

    /**
     * ProcedureController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param GlobalRoleAccessor $globalRoleAccessor
     */
    public function __construct(TranslatorInterface $translator, LoggerInterface $logger, GlobalRoleAccessor $globalRoleAccessor)
    {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->tempfile = tmpfile();
        $this->globalRoleAccessor = $globalRoleAccessor;
    }

    /**
     * @param Request $request
     * @param SignHandler $handler
     * @param DocumentFetcher $documentFetcher
     * @return JsonResponse
     * @Route("/procedure/sign-uploaded-file", name="procedure.sign.files")
     */
    public function signUploadedFileProcedure(Request $request, SignHandler $handler, DocumentFetcher $documentFetcher): JsonResponse
    {
        $command = new SignCommand(
            $fileId = $request->request->get('fileId'),
            $request->request->get('sign'),
            $request->getClientIp()
        );
        try {
            $handler->handle($command);
            $file = $documentFetcher->findDetail($fileId);
            return new JsonResponse([
                'signed_at' => $file->file_signed_at
            ]);
        } catch (\DomainException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Показывает все процедуры не зависимо от организитора и участника.
     * @param Request $request
     * @param ProcedureFetcher $procedureFetcher
     * @return Response
     * @Route("/procedures", name="procedures")
     */
    public function index(Request $request, ProcedureFetcher $procedureFetcher): Response
    {
        $this->denyAccessUnlessGranted(ProcedureAccess::INDEX);
        if ($this->isGranted('ROLE_MODERATOR') or $this->isGranted('ROLE_ADMIN')) {
            $filter = Filter::forStatus([]);

        } elseif ($this->isGranted('ROLE_USER')) {
            $filter = Filter::forStatus(
                [
                    Status::STATUS_MODERATE,
                    Status::STATUS_MODERATED,
                    Status::STATUS_ARCHIVED,
                    Status::STATUS_REJECTED,
                    Status::STATUS_NEW
                ]
            );
        }

        $form = $this->createForm(\App\ReadModel\Procedure\Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $procedureFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/procedures/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }


    /**
     * Показывает мои процедуры организатору
     * @param Request $request
     * @param string $profile_id
     * @param ProcedureFetcher $procedureFetcher
     * @param ProfileFetcher $profileFetcher
     * @param SettingsFetcher $settingsFetcher
     * @return Response
     * @Route("/procedures/{profile_id}", name="procedures.my")
     */
    public function my(Request $request, string $profile_id,
                       ProcedureFetcher $procedureFetcher,
                       ProfileFetcher $profileFetcher,
                       SettingsFetcher $settingsFetcher
    ): Response
    {

//        $this->denyAccessUnlessGranted(ProcedureAccess::INDEX);

        $profile = $profileFetcher->find($profile_id);

        $findNameOrganization = $settingsFetcher->findDetailByKey(Key::nameOrganization());

        if (!$profile->isSignContract()) {
            $this->addFlash('error', $this->translator->trans("The agreement with the %nameOrganization% ETP Operator has not been signed. There is no access to the placement of procedures on the ETP.", ['%nameOrganization%' => $findNameOrganization], 'exceptions'));
        }

        if (!$profile) {
            $this->addFlash('error', 'Заполните пожалуйста, профиль!');
            return $this->redirectToRoute('profile.create');
        }

        $filter = Filter::forUserProfile($profile->id);

        $pagination = $procedureFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/procedures/index.html.twig', [
            'pagination' => $pagination,
            'profile' => $profile
        ]);
    }

    /* //TODO НЕ УДАЛЯТЬ БЛЯТЬ!
     * @param string $procedureId
     * @param ProcedureFetcher $procedureFetcher
     * @param ProfileFetcher $profileFetcher
     * @param DocumentFetcher $documentFetcher
     * @return Response
     * @Route("/procedure/{procedureId}", name="procedure.show")

    public function show(string $procedureId, ProcedureFetcher $procedureFetcher, ProfileFetcher $profileFetcher, DocumentFetcher $documentFetcher): Response
    {
        $procedure = $procedureFetcher->findDetail($procedureId);
        if ($procedure === null) {
            throw new \DomainException('Page Not Found');
        }
        $getLots = $procedureFetcher->getLots($procedureId);

        $profile = $profileFetcher->findDetailByUserId($userId = $this->getUser()->getId());

        $files = $documentFetcher->getAllByProcedureId($procedureId);

        return $this->render('app/procedures/show.html.twig', [
            'procedure' => $procedure,
            'lots' => $getLots,
            'certificate_thumbprint' => $profile->certificate_thumbprint ?? '',
            'uploaded_files' => $files,
            'procedure_id_number' => $procedure->id_number,
            'files' => $files
        ]);

    }
    */

    /**
     * @param Request $request
     * @param string $procedureId
     * @param LotFetcher $lotFetcher
     * @param ProfileFetcher $profileFetcher
     * @param DocumentFetcher $documentFetcher
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @param Handler $handler
     * @return Response
     * @throws Exception
     * @Route("/procedure/{procedureId}", name="procedure.show")
     */
    public function show(Request $request,
                         string $procedureId,
                         LotFetcher $lotFetcher,
                         ProfileFetcher $profileFetcher,
                         DocumentFetcher $documentFetcher,
                         XmlDocumentFetcher $xmlDocumentFetcher,
                         Handler $handler
    ): Response
    {
        $procedure = $lotFetcher->findDetailByProcedureId($procedureId);
        if ($procedure === null) {
            throw new \DomainException('Page Not Found');
        }
        $lot = $lotFetcher->findDetailByProcedureId($procedureId);

        $profile = null;
        if (!$this->isGranted('ROLE_MODERATOR')){
            $profile = $profileFetcher->findDetailByUserId($userId = $this->getUser()->getId());
        }


        $files = FileHelper::rearrangeByType(
            $documentFetcher->getAll($procedureId),
            $procedure
        );


        $form = $this->createForm(\App\Model\Work\Procedure\UseCase\Lot\Open\PaymentWinnerConfirm\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Lot\Open\PaymentWinnerConfirm\Command(
                $lot->id,
                $procedureId,
                $this->getUser()->getId()
            ));

        $form->handleRequest($request);

        try {
            if ($form->get('approved')->isClicked()) {
                $handler->handleConfirm($command);
                $this->addFlash('success', $this->translator->trans('Вы успешно подтверидтили оплату имущество победителем', [], 'exceptions'));
                return $this->redirectToRoute('procedure.show', ['procedureId' => $procedureId]);
            }

            if ($form->get('reject')->isClicked()) {
                $handler->handleAnnulled($command);
                $this->addFlash('success', $this->translator->trans('Успешно', [], 'exceptions'));
                return $this->redirectToRoute('procedure.show', ['procedureId' => $procedureId]);
            }
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }





        $findLastNotificationByProcedureId = $xmlDocumentFetcher->findLastNotificationByProcedureId($procedureId);

        $filesCount = FileHelper::getFilesCount($files, $procedure);

        return $this->render('app/procedures/show.html.twig', [
            'procedure' => $procedure,
            'certificate_thumbprint' => $profile->certificate_thumbprint ?? '',
            'uploaded_files' => $files,
            'typesNames' => FileType::getFileCategories($procedure),
            'procedure_id_number' => $procedure->id_number,
            'files' => $files,
            'form' => $form->createView(),
            'filesCount' => $filesCount,
            'lastNotification' => $findLastNotificationByProcedureId
        ]);

    }


    /**
     * Клонирование процедуры
     * @param Request $request
     * @param string $procedure_id
     * @param ProcedureFetcher $procedureFetcher
     * @param \App\Model\Work\Procedure\UseCase\Duplicate\Handler $handler
     * @return Response
     * @Route("/procedure/{procedure_id}/duplicate", name="procedure.duplicate")
     */
    public function duplicate(Request $request,
                              string $procedure_id,
                              ProcedureFetcher $procedureFetcher,
                              \App\Model\Work\Procedure\UseCase\Duplicate\Handler $handler
    ): Response
    {
        $procedure = $procedureFetcher->findDetail($procedure_id);
        if ($procedure === null) {
            throw new \DomainException('Page Not Found');
        }

        try {
            $command = new \App\Model\Work\Procedure\UseCase\Duplicate\Command(
                $procedure_id,
                $userId = $this->getUser()->getId(),
                $request->getClientIp()
            );
            $handler->handle($command);

            $this->addFlash('success', $this->translator->trans('The procedure was successfully cloned.', [], 'exceptions'));
            return $this->redirectToRoute('procedures.my', ['profile_id' => $procedure->profile_id]);
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            return $this->redirectToRoute('procedure.show', ['procedureId' => $procedure_id]);


        }


    }


    /**
     * @param Request $request
     * @param string $procedureId
     * @param string $fileType
     * @param HandlerUpload $handler
     * @param FileUploader $fileUploader
     * @param ProcedureFetcher $procedureFetcher
     * @return Response
     * @throws \Exception
     * @Route("/procedure/{procedureId}/upload/{fileType}", name="procedure.upload.file")
     */
    public function upload(Request $request, string $procedureId, string $fileType, HandlerUpload $handler,
                           FileUploader $fileUploader,
                           ProcedureFetcher $procedureFetcher): Response
    {
        $procedure = $procedureFetcher->findDetail($procedureId);
        $command = new CommandUpload($procedureId, $fileType);
        $form = $this->createForm(FormUpload::class, $command);
        $form->handleRequest($request);

        $isExistsFile = $procedureFetcher->existsByTypeFile($fileType, $procedureId);

        if (!$procedure->statusIsNew() and !$procedure->statusIsRejected()) {
            $this->addFlash('error', 'Доступ запрещен.');
            return $this->redirectToRoute('procedure.show', ['procedureId' => $procedureId]);
        }

        if ($isExistsFile && $fileType != 'OTHER') {
            $this->addFlash('error', 'Разрешено загружать только 1 файл.');
            return $this->redirectToRoute('procedure.show', ['procedureId' => $procedureId]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $uploaded = $fileUploader->upload($form->get('file')->getData(), true);

            $file = new File(
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
                return $this->redirectToRoute('procedure.show', ['procedureId' => $procedureId]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/document/upload_file.html.twig', [
            'form' => $form->createView(),
            'fileType' => $fileType,
            'procedure' => $procedure

        ]);

    }

    /**
     * @param Request $request
     * @param string $procedureId
     * @param string $fileId
     * @param DeleteHandler $handler
     * @return Response
     * @Route("/procedure/{procedureId}/delete-file/{fileId}", name="procedure.delete-file", methods={"POST"})
     */
    public function deleteFile(Request $request, string $procedureId, string $fileId, DeleteHandler $handler): Response
    {
        try {
            $handler->handle(new DeleteCommand($fileId));
            $this->addFlash('success', $this->translator->trans('Файл успешно удален', [], 'exceptions'));
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }
        return $this->redirectToRoute('procedure.show', ['procedureId' => $procedureId]);
    }


    /**
     * @param Request $request
     * @param string $procedureId
     * @param ProcedureFetcher $procedureFetcher
     * @param DocumentFetcher $documentFetcher
     * @param SignXmlGenerator $signXmlGenerator ,
     * @param Streebog $streebog
     * @param \App\Model\Work\Procedure\UseCase\Sign\Handler $handler
     * @return Response
     * @Route("/procedure/{procedureId}/sign", name="procedure.sign")
     */
    public function sign(
        Request $request,
        string $procedureId,
        ProcedureFetcher $procedureFetcher,
        SignXmlGenerator $signXmlGenerator,
        Streebog $streebog,
        SerializerInterface $serializer,
        \App\Model\Work\Procedure\UseCase\Sign\Handler $handler
    ): Response
    {
        if (!$findLots = $procedureFetcher->getLots($procedureId)) {
            $this->addFlash('error', 'Перед подписаним процедуры, необходимо создать лот');
            return $this->redirectToRoute('procedure.show', ['procedureId' => $procedureId]);
        }

        $procedure = $procedureFetcher->findDetail($procedureId);

        $xml = $signXmlGenerator->generate($procedure);
        $hash = $streebog->getHash($xml);


        $form = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Sign\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Sign\Command(
                $procedureId,
                $xml,
                $hash,
                $this->getUser()->getId(),
                $request->getClientIp()
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Ваш запрос на модерацию торговой процедуры №'.$procedure->id_number.' отправлен');
                return $this->redirectToRoute('procedure.show', ['procedureId' => $procedureId]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        $xmlD = $serializer->deserialize($xml, NotifyDetailView::class, 'xml');

        return $this->render('app/procedures/sign.html.twig', [
            'procedure' => $procedure,
            'hash' => $hash,
            'form' => $form->createView(),
            'xml' => $xmlD
        ]);

    }


    /**
     * @param Request $request
     * @param string $procedure_id
     * @param LotFetcher $lotFetcher
     * @param \App\Model\Work\Procedure\UseCase\Edit\Handler $handler
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @throws Exception
     * @Route("/procedure/{procedure_id}/edit", name="procedure.edit")
     */
    public function edit(
        Request $request,
        string $procedure_id,
        LotFetcher $lotFetcher,
        \App\Model\Work\Procedure\UseCase\Edit\Handler $handler, ProfileFetcher $profileFetcher
    ): Response
    {
        $procedureDetail = $lotFetcher->findDetailByProcedureId($procedure_id);
        $command = \App\Model\Work\Procedure\UseCase\Edit\Command::edit(
            $procedure_id,
            $procedureDetail,
            $userId = $this->getUser()->getId(),
            $request->getClientIp()
        );

        $form = $this->createForm(\App\Model\Work\Procedure\UseCase\Edit\Form::class, $command, ['user_id' => $userId]);
        $form->handleRequest($request);
        $organizerProfile = $profileFetcher->find($this->getUser()->getProfileId());
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Procedure changed successfully.', [], 'exceptions'));
                return $this->redirectToRoute('procedure.show', ['procedureId' => $procedure_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/edit.html.twig', [
            'form' => $form->createView(),
            'procedure' => $procedureDetail,
            'organizerProfile' => $organizerProfile
        ]);
    }

    /**
     * @param string $file_id
     * @param DocumentFetcher $documentFetcher
     * @param FileUploader $fileUploader
     * @return BinaryFileResponse
     * @Route("/procedure/download/{file_id}", name="procedure.download")
     */
    public function downloadFile(string $file_id,
                                 DocumentFetcher $documentFetcher,
                                 FileUploader $fileUploader): BinaryFileResponse
    {
        $file = $documentFetcher->findDetail($file_id);

        if (!is_null($file)) {
            $formattedFileRealName = str_replace('.', '_', $file->file_real_name);

            $zipfile = stream_get_meta_data($this->tempfile)['uri'];
            $generateUrlFile = $fileUploader->generateUrl($file->file_path . '/' . $file->file_name);
            $zip = new \ZipArchive();

            $extension = explode('.', $file->file_name);
            $zip->open($zipfile, \ZipArchive::CREATE);
            $zip->addFromString($file->file_real_name . '.' . $extension[1], file_get_contents($generateUrlFile));

            $zip->addFromString('hash.txt', $file->file_hash ?? '');
            $zip->addFromString('sign.sig', $file->file_sign ?? '');
            $zip->close();

            $response = new BinaryFileResponse($zipfile);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $formattedFileRealName . '.zip');
        } else
            $response = new BinaryFileResponse($file_id, Response::HTTP_NOT_FOUND);

        return $response;
    }


}
