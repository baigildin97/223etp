<?php
declare(strict_types=1);

namespace App\Controller\Procedure\Lot\Bid;

use App\Model\Admin\Entity\Settings\Key;
use App\Model\User\Entity\User\UserRepository;
use App\Model\Work\Procedure\Entity\Document\FileType;
use App\Model\Work\Procedure\Entity\Lot\Bid\Id;
use App\Model\Work\Procedure\Entity\Lot\Status;
use App\Model\Work\Procedure\Services\Bid\Sign\SignXmlGenerator;
use App\Model\Work\Procedure\Services\Bid\Sign\XmlDetailView;
use App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Recall\Command;
use App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Recall\Form;
use App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Recall\Handler;
use App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Upload;
use App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Create;
use App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Delete;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Procedure\Bid\Document\DocumentFetcher;
use App\ReadModel\Procedure\Bid\Filter\Filter;
use App\ReadModel\Procedure\Bid\History\DetailView;
use App\ReadModel\Procedure\Bid\XmlDocument\XmlDocumentFetcher;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Services\Document\BrickDocument;
use App\Services\Hash\Streebog;
use App\Services\Uploader\FileUploader;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Action\{
    Command as CommandCause,
    Form as FormCause
};

/**
 * Class IndexController
 * @package App\Controller\Procedure\Lot\Bid
 */
class IndexController extends AbstractController
{

    private $tempfile;

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
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var RequisiteFetcher
     */
    private $requisiteFetcher;

    /**
     * IndexController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param ProfileFetcher $profileFetcher
     * @param RequisiteFetcher $requisiteFetcher
     */
    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        ProfileFetcher $profileFetcher,
        RequisiteFetcher $requisiteFetcher
    )
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->profileFetcher = $profileFetcher;
        $this->requisiteFetcher = $requisiteFetcher;
        $this->tempfile = tmpfile();
    }

    /**
     * Показывает все заявки кроме Черновика
     * @param Request $request
     * @param string $profile_id
     * @param BidFetcher $bidFetcher
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @throws Exception
     * @Route("/bids/{profile_id}", name="bids")
     */
    public function index(Request $request, string $profile_id, BidFetcher $bidFetcher, ProfileFetcher $profileFetcher): Response
    {
        if (!$profile = $profileFetcher->find($profile_id)) {
            $this->addFlash('error', 'Заполните пожалуйста, профиль!');
            return $this->redirectToRoute('procedure.create');
        }

        $filter = Filter::forUserProfileStatusDraft($profile->id,
            [\App\Model\Work\Procedure\Entity\Lot\Bid\Status::sent()->getName(),
                \App\Model\Work\Procedure\Entity\Lot\Bid\Status::recalled()->getName(),
                \App\Model\Work\Procedure\Entity\Lot\Bid\Status::reject()->getName(),
                \App\Model\Work\Procedure\Entity\Lot\Bid\Status::approved()->getName()]
        );

        $pagination = $bidFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/procedures/lot/bid/index.html.twig', [
            'bids' => $pagination
        ]);
    }


    /**
     * Показывает заявки в статусе "Черновик"
     * @param Request $request
     * @param string $profile_id
     * @param BidFetcher $bidFetcher
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @throws Exception
     * @Route("/bids/draft/{profile_id}", name="bids.draft")
     */
    public function draft(Request $request, string $profile_id, BidFetcher $bidFetcher, ProfileFetcher $profileFetcher): Response
    {
        if (!$profile = $profileFetcher->find($profile_id)) {
            $this->addFlash('error', 'Заполните пожалуйста, профиль!');
            return $this->redirectToRoute('procedure.create');
        }

        $filter = Filter::forUserProfileStatusDraft(
            $profile->id,
            [\App\Model\Work\Procedure\Entity\Lot\Bid\Status::new()->getName()
            ]);

        $pagination = $bidFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/procedures/lot/bid/index.html.twig', [
            'bids' => $pagination
        ]);
    }


    /**
     * Детальный просмотр заявки
     * @param Request $request
     * @param string $bidId
     * @param BidFetcher $bidFetcher
     * @param DocumentFetcher $documentFetcher
     * @param ProfileFetcher $profileFetcher
     * @param SerializerInterface $serializer
     * @param \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Action\Handler $handler
     * @return Response
     * @Route("/bid/{bidId}", name="bid.show")
     */
    public function show(
        Request $request,
        string $bidId,
        BidFetcher $bidFetcher,
        DocumentFetcher $documentFetcher,
        ProfileFetcher $profileFetcher,
        SerializerInterface $serializer,
        \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Action\Handler $handler
    ): Response
    {
        $bid = $bidFetcher->findDetail($bidId);

        $getRoleCurrentUser = $profileFetcher->findDetailByUserId($this->getUser()->getId());
        $filter = \App\ReadModel\Procedure\Bid\Document\Filter\Filter::fromBid($bidId);

        $documents = $documentFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        $form = $this->createForm(FormCause::class, $command = new CommandCause($bidId));

        $form->handleRequest($request);
        try {
            if ($form->get('approved')->isClicked()) {
                $handler->handleApprove($command);
                return $this->redirectToRoute('lot.bids', ['lotId' => $bid->lot_id]);
            }

            if ($form->get('reject')->isClicked()) {
                $handler->handleReject($command);
                return $this->redirectToRoute('lot.bids', ['lotId' => $bid->lot_id]);
            }
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }

        $xmlFile = null;

        //dd($bid);

        return $this->render('app/procedures/lot/bid/show.html.twig', [
            'bid' => $bid,
            'profile' => $getRoleCurrentUser,
            'documents' => $documents,
            'xml_file' => $xmlFile,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $lotId
     * @param LotFetcher $lotFetcher
     * @param DocumentFetcher $fileFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @param Create\Handler $handler
     * @param FileUploader $fileUploader
     * @param RequisiteFetcher $requisiteFetcher
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @throws \Exception
     * @Route("/bid/apply/{lotId}", name="bid.apply")
     */
    public function create(
        Request $request,
        string $lotId,
        LotFetcher $lotFetcher,
        DocumentFetcher $fileFetcher,
        ProcedureFetcher $procedureFetcher,
        Create\Handler $handler,
        FileUploader $fileUploader,
        RequisiteFetcher $requisiteFetcher,
        ProfileFetcher $profileFetcher
    ): Response
    {
        $profile = $profileFetcher->findDetailByUserId($this->getUser()->getId());
        $lot = $lotFetcher->findDetail($lotId);

        //TODO не доделан.
        if (!$requisite = $requisiteFetcher->existsActiveRequisiteForPaymentId($profile->payment_id)) {
            $this->addFlash('error', 'Необходимо добавить реквизиты.');
            return $this->redirectToRoute('lot.show', ['lotId' => $lot->id]);
        }

        if ($profile->subscribe_tariff_id === null) {
            $this->addFlash('error', 'Необходимо активировать тарифный план.');
            return $this->redirectToRoute('lot.show', ['lotId' => $lot->id]);
        }


        if ($lot->status !== Status::acceptingApplications()->getName()) {
            $this->addFlash('error', 'Доступ запрещен.');
            return $this->redirectToRoute('procedure.show', ['procedureId' => $lot->procedure_id]);
        }

        $procedure = $procedureFetcher->findIdNumberByProcedure($procedureId = $lot->procedure_id);


        $form = $this->createForm(Create\Form::class, $command = new Create\Command(
            $userId = $this->getUser()->getId(),
            $lot->id, $request->getClientIp(),
            $bidId = Id::next()
        ), ['user_id' => $userId]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                // $this->addFlash('success', 'Заявка успешно создана.');
                return $this->redirectToRoute('bid.show', ['bidId' => $bidId]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/lot/bid/create.html.twig', [
            'lot' => $lot,
            'procedure' => $procedure,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $id
     * @param BidFetcher $bidFetcher
     * @param DocumentFetcher $documentFetcher
     * @param SignXmlGenerator $signXmlGenerator
     * @param Streebog $streebog
     * @param \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Sign\Handler $handler
     * @param SettingsFetcher $settingsFetcher
     * @param ProfileFetcher $profileFetcher
     * @param RequisiteFetcher $requisiteFetcher
     * @param SerializerInterface $serializer
     * @return Response
     * @Route("/bid/{id}/sign", name="bid.sign")
     */
    public function sign(
        Request $request,
        string $id,
        BidFetcher $bidFetcher,
        DocumentFetcher $documentFetcher,
        SignXmlGenerator $signXmlGenerator,
        Streebog $streebog,
        \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Sign\Handler $handler,
        SettingsFetcher $settingsFetcher,
        ProfileFetcher $profileFetcher,
        RequisiteFetcher $requisiteFetcher,
        SerializerInterface $serializer
    ): Response
    {
        $profile = $profileFetcher->findDetailByUserId($this->getUser()->getId());

        $bid = $bidFetcher->findDetail($id);

        $documents = $documentFetcher->all(
            \App\ReadModel\Procedure\Bid\Document\Filter\Filter::fromBid($bid->id),
            $request->query->getInt('page', 1),
            10
        );

        $findRequisit = $requisiteFetcher->findDetail($bid->requisite_id);
        $xml = $signXmlGenerator->generate($bid, $profile, $findRequisit);
        $hash = $streebog->getHash($xml);
        $form = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Sign\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Sign\Command($bid->id, $xml, $hash)
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Заявка на участие в торгах отправлена оператору.');
                return $this->redirectToRoute('bid.show', ['bidId' => $id]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        $findNameOrganization = $settingsFetcher->findDetailByKey(Key::nameOrganization());
        $findServerName = $settingsFetcher->findDetailByKey(Key::siteDomain());
        $xmlDeserialize = $serializer->deserialize($xml, XmlDetailView::class, 'xml');

        return $this->render('app/procedures/lot/bid/sign.html.twig', [
            'bid' => $bid,
            'xmlDeserialize' => $xmlDeserialize,
            'hash' => $hash,
            'request' => $findRequisit,
            'form' => $form->createView(),
            'documents' => $documents,
            'profile' => $profile,
            'server_name' => $findServerName,
            'name_organization' => $findNameOrganization
        ]);
    }

    /**
     * @param Request $request
     * @param string $bidId
     * @param BidFetcher $bidFetcher
     * @param DocumentFetcher $documentFetcher
     * @param Streebog $streebog
     * @param SignXmlGenerator $signXmlGenerator
     * @return Response
     * @Route("/bid/{bidId}/recall", name="bid.recall")
     */
    public function recall(Request $request, string $bidId, BidFetcher $bidFetcher, DocumentFetcher $documentFetcher, Streebog $streebog, SignXmlGenerator $signXmlGenerator, Handler $handler): Response
    {
        $bid = $bidFetcher->findDetail($bidId);
        $documents = $documentFetcher->all(
            \App\ReadModel\Procedure\Bid\Document\Filter\Filter::fromBid($bid->id),
            $request->query->getInt('page', 1),
            10
        );
        $profile = $this->profileFetcher->findDetailByUserId($this->getUser()->getId());
        $requisite = $this->requisiteFetcher->findDetail($bid->requisite_id);

        $xml = $signXmlGenerator->generate($bid, $profile, $requisite);
        $hash = $streebog->getHash($xml);

        $form = $this->createForm(
            Form::class,
            $command = new Command($bid->id, $xml, $hash)
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Заявка на участие в торгах отозвана.');
                return $this->redirectToRoute('bid.show', ['bidId' => $bidId]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/lot/bid/recall.html.twig', [
            'bid' => $bid,
            'hash' => $hash,
            'form' => $form->createView(),
            'documents' => $documents
        ]);
    }

    /**
     * @Route("/bid/{bidId}/upload", name="bid.file.upload")
     * @param Request $request
     * @param string $bidId
     * @param FileUploader $fileUploader
     * @param Upload\Handler $handler
     * @param BidFetcher $bidFetcher
     * @param DocumentFetcher $documentFetcher
     * @param BrickDocument $brickDocument
     * @return Response
     * @throws \Exception
     */
    public function upload(Request $request,
                           string $bidId,
                           FileUploader $fileUploader,
                           Upload\Handler $handler,
                           BidFetcher $bidFetcher,
                           DocumentFetcher $documentFetcher,
                           BrickDocument $brickDocument): Response
    {
        $bid = $bidFetcher->findDetail($bidId);

        $command = new Upload\Command($bid->id, $request->getClientIp());
        $form = $this->createForm(Upload\Form::class, $command);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $brickDocumentBool = false;
            if ($countBrickFiles = $brickDocument->rules($bid->procedure_id, $this->getUser()->getId())) {

                $countNewFilesByProfileId = $documentFetcher->countNewFiles($bid->id);
                if ($countNewFilesByProfileId < $countBrickFiles) {
                    $brickDocumentBool = true;
                }
            }

            $uploaded = $fileUploader->upload($form->get('file')->getData(), true, $brickDocumentBool);



            $file = new Upload\File(
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
                return $this->redirectToRoute('bid.show', ['bidId' => $bidId]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/lot/bid/document/upload.html.twig', [
            'form' => $form->createView(),
            'bid' => $bid
        ]);
    }

    /**
     * @param string $bidId
     * @param string $documentId
     * @param Delete\Handler $handler
     * @return Response
     * @Route("/bid/{bidId}/document/{documentId}/delete", name="bid.document.delete", methods={"POST"})
     */
    public function documentDelete(string $bidId, string $documentId, Delete\Handler $handler): Response
    {
        try {
            $handler->handle(new Delete\Command($bidId, $documentId));
            $this->addFlash('success', $this->translator->trans('File deleted success.', [], 'exceptions'));
        } catch (\DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
        }
        return $this->redirectToRoute('bid.show', ['bidId' => $bidId]);
    }

    /**
     * @param Request $request
     * @param string $bidId
     * @param string $documentId
     * @param Upload\Sign\Handler $handler
     * @return Response
     * @Route("/bid/{bidId}/document/{documentId}/sign", name="bid.document.sign", methods={"POST"})
     */
    public function signUploadedFile(
        Request $request, string $bidId, string $documentId, Upload\Sign\Handler $handler,
        DocumentFetcher $documentFetcher): JsonResponse
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $command = new Upload\Sign\Command(
                $documentId,
                $bidId,
                $request->get('sign'),
                $request->getClientIp(),
                $this->getUser()->getId()
            );
            try {
                $handler->handle($command);
                $file = $documentFetcher->findDetail($documentId);
                return new JsonResponse([
                    'signed_at' => $file->signed_at
                ]);
            } catch (\DomainException $e) {
                return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Одобрение заявки|Отклонения заявки|для организатора
     * @param Request $request
     * @param string $bidId
     * @param BidFetcher $bidFetcher
     * @param DocumentFetcher $documentFetcher
     * @param \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Sign\Handler $handler
     * @param Streebog $streebog
     * @param UserRepository $userRepository
     * @param \App\Model\Work\Procedure\Services\Bid\Organizer\SignXmlGenerator $signXmlGenerator
     * @return Response
     * @Route("/bid/{bidId}/sign-organizer", name="bid.sign.organizer")
     */
    public function signOrganizer(Request $request,
                                  string $bidId,
                                  BidFetcher $bidFetcher,
                                  DocumentFetcher $documentFetcher,
                                  \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Sign\Handler $handler,
                                  Streebog $streebog,
                                  UserRepository $userRepository,
                                  \App\Model\Work\Procedure\Services\Bid\Organizer\SignXmlGenerator $signXmlGenerator): Response
    {
        $bid = $bidFetcher->findDetail($bidId);

        $documents = $documentFetcher->all(
            \App\ReadModel\Procedure\Bid\Document\Filter\Filter::fromBid($bidId),
            $request->query->getInt('page', 1),
            10
        );
        $xml = $signXmlGenerator->generate($bid);
        $hash = $streebog->getHash($xml);

        $form = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Sign\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Sign\
            Command($bidId, $xml, $hash, $request->getClientIp())
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Успешно');
                return $this->redirectToRoute('bid.show', ['bidId' => $bidId]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/lot/bid/organizer/sign.html.twig', [
            'bid' => $bid,
            'hash' => $hash,
            'form' => $form->createView(),
            'documents' => $documents
        ]);

    }


    /**
     * @param Request $request
     * @param string $bid_id
     * @param string $file_id
     * @param BidFetcher $bidFetcher
     * @param DocumentFetcher $documentFetcher
     * @param FileUploader $fileUploader
     * @return Response
     * @Route("/bid/{bid_id}/file/{file_id}/download", name="bid.file.download")
     */
    public function download(Request $request,
                             string $bid_id,
                             string $file_id,
                             BidFetcher $bidFetcher,
                             DocumentFetcher $documentFetcher,
                             FileUploader $fileUploader): Response
    {
        $document = $documentFetcher->findDetail($file_id);

        if ($document === null) {
            throw new \DomainException('Page Not Found');
        }

        if (!is_null($document)) {
            $formattedFileRealName = str_replace('.', '_', $document->file_real_name);

            //TODO не доделан файлы не выкачивает
            $zipfile = stream_get_meta_data($this->tempfile)['uri'];
            $generateUrlFile = $fileUploader->generateUrl($document->file_path . '/' . $document->file_name);
            $zip = new \ZipArchive();
            $s = file_get_contents("http://new.229etp.ru/");
            var_dump($s);
            die();


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://ftp.229etp.ru/1.txt",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET"
            ));

            $response = curl_exec($curl);
            dd($response);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                dd($err);
            } else {
                echo $response;
            }


            $extension = explode('.', $document->file_name);
            $zip->open($zipfile, \ZipArchive::CREATE);
            $zip->addFromString($document->file_real_name . '.' . $extension[1], file_get_contents($generateUrlFile));
            $zip->addFromString('hash.txt', $document->file_hash);
            $zip->addFromString('sign.sig', $document->file_sign);
            $zip->close();

            $response = new BinaryFileResponse($zipfile);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $formattedFileRealName . '.zip');
        }
        return new BinaryFileResponse($bid_id, Response::HTTP_NOT_FOUND);
    }

}
