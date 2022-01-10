<?php
declare(strict_types=1);

namespace App\Controller\Profile;


use App\Helpers\Filter;
use App\Model\User\Entity\Profile\Document\FileType;
use App\Model\User\Service\Profile\Accreditation\Recall\RecallXmlGenerator;
use App\Model\User\Service\Profile\Accreditation\Sign\DetailXmlView;
use App\Model\User\Service\Profile\Accreditation\Sign\SignXmlGenerator;
use App\Model\User\Service\Profile\File\FileHelper;
use App\Model\User\UseCase\Profile\Document\Delete\Command as DeleteCommand;
use App\Model\User\UseCase\Profile\Document\Delete\Handler as DeleteHandler;
use App\Model\User\UseCase\Profile\Document\Sign\Command;
use App\Model\User\UseCase\Profile\Document\Sign\Handler;
use App\Model\User\UseCase\Profile\Accredation\Sign\Form;
use App\Model\User\UseCase\Profile\Document\Upload\File;
use App\ReadModel\Certificate\CertificateFetcher;
use App\ReadModel\Profile\Document\DocumentFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use App\Security\Voter\ProfileAccess;
use App\Services\Hash\Streebog;
use App\Services\Uploader\FileUploader;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Model\User\UseCase\Profile\ChangeCertificate\IndividualEntrepreneur\Command as CommandChangeIndividualEntrepreneur;
use App\Model\User\UseCase\Profile\ChangeCertificate\IndividualEntrepreneur\Form as FormChangeIndividualEntrepreneur;
use App\Model\User\UseCase\Profile\ChangeCertificate\IndividualEntrepreneur\Handler as HandlerChangeIndividualEntrepreneur;
use App\Model\User\UseCase\Profile\ChangeCertificate\Individual\{
    Command as CommandChangeIndividual,
    Form as FormChangeIndividual,
    Handler as HandlerChangeIndividual
};
use App\Model\User\UseCase\Profile\Document\Upload\{
    Command as CommandUpload,
    Form as FormUpload,
    Handler as HandlerUpload
};

/**
 * Class IndexController
 * @package App\Controller\Profile
 */
class IndexController extends AbstractController
{

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

    /**
     * IndexController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param ProfileFetcher $profileFetcher
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, ProfileFetcher $profileFetcher)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->profileFetcher = $profileFetcher;
    }

    /**
     * @param ProfileFetcher $profileFetcher
     * @param string $profile_id
     * @param DocumentFetcher $documentFetcher
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @return Response
     * @Route("/profile/{profile_id}", name="profile")
     */
    public function index(
        ProfileFetcher $profileFetcher,
        string $profile_id,
        DocumentFetcher $documentFetcher,
        XmlDocumentFetcher $xmlDocumentFetcher
    ): Response {

        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $profileFetcher->find($profile_id)
        );

        $files = FileHelper::rearrangeByType(
            $documentFetcher->getAll($profile->id),
            $profile
        );

        $filesCount = FileHelper::getFilesCount($files, $profile);

        $issetDocuments = $xmlDocumentFetcher->issetDocuments($profile_id);

        return $this->render('app/profile/index.html.twig', [
            'profile' => $profile,
            'files' => $files,
            'filesCount' => $filesCount,
            'typesNames' => FileType::getFileCategories($profile),
            'certificate_thumbprint' => $profile->certificate_thumbprint,
            'issetDocuments' => $issetDocuments
        ]);
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param Handler $handler
     * @return Response
     * @Route("/profile/{profile_id}/sign-uploaded-file", name="profile.sign")
     */
    public function signUploadedFile(Request $request, string $profile_id, Handler $handler, DocumentFetcher $documentFetcher): JsonResponse
    {
        $this->isGranted('ROLE_MANAGE_PROFILE');
        $command = new Command(
            $fileId = $request->request->get('fileId'),
            $request->request->get('sign'),
            $request->getClientIp(),
            $profile_id
        );

        try {
            $handler->handle($command);
            $file = $documentFetcher->getDetail($fileId);
            return new JsonResponse([
                'signed_at' => Filter::date($file->file_sign_at)
            ]);
        } catch (\DomainException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param ProfileFetcher $profileFetcher
     * @param SerializerInterface $serializer
     * @param SignXmlGenerator $signXmlGenerator
     * @param Streebog $streebog
     * @param \App\Model\User\UseCase\Profile\Accredation\Sign\Handler $handler
     * @return Response
     * @Route("/profile/{profile_id}/accreditation", name="profile.accreditation")
     * @throws Exception
     */
    public function accreditationStatement(
        Request $request,
        string $profile_id,
        ProfileFetcher $profileFetcher,
        SerializerInterface $serializer,
        SignXmlGenerator $signXmlGenerator,
        Streebog $streebog,
        \App\Model\User\UseCase\Profile\Accredation\Sign\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_ACCREDITATION,
            $profile = $profileFetcher->find($profile_id)
        );

        if ($profileFetcher->existsNotSignDocument($profile_id)){
            $this->addFlash('success', 'Подписаны не все документы профиля.');
            return $this->redirectToRoute('profile', ['profile_id' => $profile->id]);
        }

        $xml = $signXmlGenerator->generate($profile);
        $hash = $streebog->getHash($xml);

        $form = $this->createForm(
            Form::class,
            $command = new \App\Model\User\UseCase\Profile\Accredation\Sign\Command($profile->id, $xml, $hash, $request->getClientIp())
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Ваше заявление отправлено на рассмотрение Оператору ЭТП.');
                return $this->redirectToRoute('profile.xml_documents', ['profile_id' => $profile->id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }
        $xml = $serializer->deserialize($xml, DetailXmlView::class, 'xml');


        return $this->render('app/profile/accreditation/index.html.twig', [
            'xml' => $xml,
            'profile' => $profile,
            'hash' => $hash,
            'form' => $form->createView(),
            'typesNames' => FileType::getFileCategories($profile),
            'certificate_thumbprint' => $profile->certificate_thumbprint
        ]);
    }


    /**
     * @param Request $request
     * @param string $profile_id
     * @param string $xml_document_id
     * @param ProfileFetcher $profileFetcher
     * @param \App\Model\User\UseCase\Profile\Accredation\Recall\Simple\Handler $handler
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @param RecallXmlGenerator $recallXmlGenerator
     * @param Streebog $streebog
     * @param SerializerInterface $serializer
     * @return Response
     * @throws Exception
     * @Route("/profile/{profile_id}/accreditation/{xml_document_id}/recall", name="profile.accreditation.recall")
     */
    public function recall(
        Request $request,
        string $profile_id,
        string $xml_document_id,
        ProfileFetcher $profileFetcher,
        \App\Model\User\UseCase\Profile\Accredation\Recall\Simple\Handler $handler,
        XmlDocumentFetcher $xmlDocumentFetcher,
        RecallXmlGenerator $recallXmlGenerator,
        Streebog $streebog,
        SerializerInterface $serializer
    ): Response
    {
        $xmlDocument = $xmlDocumentFetcher->find($xml_document_id);

        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_ACCREDITATION,
            $profile = $profileFetcher->find($profile_id)
        );

        $xml = $recallXmlGenerator->generate($xmlDocument);
        $hash = $streebog->getHash($xml);


        $form = $this->createForm(
            \App\Model\User\UseCase\Profile\Accredation\Recall\Simple\Form::class,
            $command = new \App\Model\User\UseCase\Profile\Accredation\Recall\Simple\Command(
                $profile_id,
                $xml_document_id,
                $request->getClientIp(),
                $xml,
                $hash
            )
        );

        $form->handleRequest($request);

        if ($xmlDocument->isTypeStatementRegistration()){
            $flush = 'Ваше заявление на регистрацию отозвано.';
        }

        if ($xmlDocument->isTypeStatementEdit()){
            $flush = 'Ваше заявление на изменение данных пользователя отозвано.';
        }

        if ($xmlDocument->isTypeReplacingEp()){
            $flush = 'Ваше заявление на замену электронной подписи отозвано.';
        }

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans($flush, [], 'exceptions'));
                return $this->redirectToRoute('profile.xml_documents', ['profile_id' => $profile_id]);
            } catch (\DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        $xmlDeserialize = $serializer->deserialize($xml, \App\Model\User\Service\Profile\Accreditation\Recall\DetailXmlView::class, 'xml');

        return $this->render('app/profile/xml_document/recall.html.twig', [
            'xml_document' => $xmlDocument,
            'xml' => $xmlDeserialize,
            'hash' => $hash,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $profile_id
     * @param string $fileType
     * @param HandlerUpload $handler
     * @param FileUploader $fileUploader
     * @return Response
     * @throws \Exception
     * @Route("/profile/{profile_id}/upload/{fileType}", name="profile.upload.file")
     */
    public function upload(Request $request, string $profile_id, string $fileType, HandlerUpload $handler, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_UPLOAD_FILE,
            $profile = $this->profileFetcher->find($profile_id)
        );

        $profile = $this->profileFetcher->find($profile_id);

        $command = new CommandUpload($this->getUser()->getId(), $fileType);

        $form = $this->createForm(FormUpload::class, $command, ['file_type' => $fileType]);

        $form->handleRequest($request);

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
                return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/upload_file.html.twig', [
            'form' => $form->createView(),
            'fileType' => $fileType,
            'profile' => $profile
        ]);
    }


    /**
     * Delete uploaded file
     * @param string $profile_id
     * @param string $fileId
     * @param DeleteHandler $handler
     * @return Response
     * @Route("/profile/{profile_id}/delete-file/{fileId}", name="profile.delete-file")
     */
    public function deleteFile(string $profile_id, string $fileId, DeleteHandler $handler): Response
    {
        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_DELETE_FILE,
            $profile = $this->profileFetcher->find($profile_id)
        );

        try {
            $handler->handle(new DeleteCommand($fileId));
            $this->addFlash('success', $this->translator->trans('Файл успешно удален', [], 'exceptions'));
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }
        return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
    }

    /**
     * Изменения сертификата юр. лица.
     * @param Request $request
     * @param string $profile_id
     * @param \App\Model\User\UseCase\Profile\ChangeCertificate\LegalEntity\Handler $handler
     * @return Response
     * @Route("/profile/{profile_id}/change/certificate/legal-entity", name="profile.certificate.change.legal.entity")
     */
    public function certificateChangeLegalEntity(
        Request $request,
        string $profile_id,
        \App\Model\User\UseCase\Profile\ChangeCertificate\LegalEntity\Handler $handler
    ): Response
    {

        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->find($profile_id)
        );

        $command = new \App\Model\User\UseCase\Profile\ChangeCertificate\LegalEntity\Command(
            $userId = $this->getUser()->getId(),
            $request->getClientIp()
        );

        $form = $this->createForm(
            \App\Model\User\UseCase\Profile\ChangeCertificate\LegalEntity\Form::class,
            $command, ['user_id' => $userId]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                if ($profile->getStatus()->isActive()) {
                    $this->addFlash('success', $this->translator->trans('Сертификат профиля успешно изменен. Для активации профиля необходимо отправить заявление на редактирование Оператору ЭТП.', [], 'exceptions'));
                } else {
                    $this->addFlash('success', $this->translator->trans('Сертификат профиля успешно изменен. Необходимо подписать и отправить заявление на регистрацию Оператору ЭТП', [], 'exceptions'));
                }
                return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/change_certificate/legal_entity.html.twig', [
            'form' => $form->createView(),
            'profile' => $this->profileFetcher->find($profile_id)
        ]);
    }

    /**
     * Изменения сертификата индивидуального предпринимателя
     * @param Request $request
     * @param string $profile_id
     * @param HandlerChangeIndividualEntrepreneur $handler
     * @return Response
     * @Route("/profile/{profile_id}/changeeds/individual-entrepreneur", name="profile.certificate.change.individual.entrepreneur")
     */
    public function certificateChangeIndividualEntrepreneur(
        Request $request,
        string $profile_id,
        HandlerChangeIndividualEntrepreneur $handler
    ): Response
    {

        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->find($profile_id)
        );

        $command = new CommandChangeIndividualEntrepreneur($userId = $this->getUser()->getId(), $request->getClientIp());
        $form = $this->createForm(FormChangeIndividualEntrepreneur::class, $command, ['user_id' => $userId]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                if ($profile->getStatus()->isActive()) {
                    $this->addFlash('success', $this->translator->trans('Сертификат профиля успешно изменен. Для активации профиля необходимо отправить заявление на редактирование Оператору ЭТП.', [], 'exceptions'));
                } else {
                    $this->addFlash('success', $this->translator->trans('Сертификат профиля успешно изменен. Необходимо подписать и отправить заявление на регистрацию Оператору ЭТП', [], 'exceptions'));
                }

                return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/change_certificate/individual_entrepreneur.html.twig', [
            'form' => $form->createView(),
            'profile' => $this->profileFetcher->find($profile_id)
        ]);
    }

    /**
     * Изменения сертификата физическое лицо
     * @param Request $request
     * @param string $profile_id
     * @param HandlerChangeIndividual $handler
     * @return Response
     * @Route("/profile/{profile_id}/changeeds/individual", name="profile.certificate.change.individual")
     */
    public function certificateChangeIndividual(
        Request $request,
        string $profile_id,
        HandlerChangeIndividual $handler
    ): Response
    {

        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->find($profile_id)
        );

        $command = new CommandChangeIndividual($userId = $this->getUser()->getId(), $request->getClientIp());
        $form = $this->createForm(FormChangeIndividual::class, $command, ['user_id' => $userId]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                if ($profile->getStatus()->isActive()) {
                    $this->addFlash('success', $this->translator->trans('Сертификат профиля успешно изменен. Для активации профиля необходимо отправить заявление на редактирование Оператору ЭТП.', [], 'exceptions'));
                } else {
                    $this->addFlash('success', $this->translator->trans('Сертификат профиля успешно изменен. Необходимо подписать и отправить заявление на регистрацию Оператору ЭТП', [], 'exceptions'));
                }
                return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/profile/change_certificate/individual.html.twig', [
            'form' => $form->createView(),
            'profile' => $this->profileFetcher->find($profile_id)
        ]);
    }


}