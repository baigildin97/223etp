<?php
declare(strict_types=1);
namespace App\Controller\Admin\Moderator;

use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Procedure\Bid\Document\DocumentFetcher;
use App\ReadModel\Procedure\Bid\Filter\Filter;
use App\ReadModel\Procedure\Bid\History\DetailView;
use App\ReadModel\Procedure\Bid\XmlDocument\XmlDocumentFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BidsController
 * @package App\Controller\Admin\Moderator
 * @IsGranted("ROLE_MODERATOR")
 */
class BidsController extends AbstractController
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
     * ProcedureController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param BidFetcher $bidFetcher
     * @return Response
     * @Route("/admin/bids", name="moderate.bids")
     */
    public function list(Request $request, BidFetcher $bidFetcher): Response
    {
        $filter = Filter::all();

        $form = $this->createForm(\App\ReadModel\Procedure\Bid\Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $bidFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/admin/moderator/bids/index.html.twig', [
            'form' => $form->createView(),
            'bids' => $pagination
        ]);
    }

    /**
     * Детальный просмотр заявки модераторм либо админстратором
     * @param Request $request
     * @param string $bid_id
     * @param BidFetcher $bidFetcher
     * @param DocumentFetcher $documentFetcher
     * @param ProfileFetcher $profileFetcher
     * @param SerializerInterface $serializer
     * @return Response
     * @Route("/admin/bid/{bid_id}", name="moderate.bid.show")
     */
    public function show(
        Request $request,
        string $bid_id,
        BidFetcher $bidFetcher,
        DocumentFetcher $documentFetcher,
        ProfileFetcher $profileFetcher,
        SerializerInterface $serializer
    ): Response {
        $bid = $bidFetcher->findDetail($bid_id);

        $getRoleCurrentUser = $profileFetcher->findDetailByUserId($this->getUser()->getId());
        $filter = \App\ReadModel\Procedure\Bid\Document\Filter\Filter::fromBid($bid_id);

        $documents = $documentFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );


        return $this->render('app/admin/moderator/bids/show.html.twig', [
            'bid' => $bid,
            'profile' => $getRoleCurrentUser,
            'documents' => $documents
        ]);
    }


    /**
     * @param Request $request
     * @param string $bid_id
     * @param string $history_id
     * @param BidFetcher $bidFetcher
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @return Response
     * @Route("/admin/{bid_id}/history/{history_id}/download", name="moderate.bid.history.download")
     */
    public function download(Request $request, string $bid_id, string $history_id, BidFetcher $bidFetcher, XmlDocumentFetcher $xmlDocumentFetcher): Response
    {
        $bid = $bidFetcher->findDetail($bid_id);
        $findXmlFile = $xmlDocumentFetcher->findDetailXmlFile($history_id);
        if($findXmlFile === null){
            throw new \DomainException('Page Not Found');
        }

        if (!is_null($findXmlFile)) {
            $zipFile = stream_get_meta_data(tmpfile())['uri'];
            $archiveName = 'BID_№' . $bid->number;
            $zip = new \ZipArchive();

            $zip->open($zipFile, \ZipArchive::CREATE);
            $zip->addFile($archiveName . '.xml', $findXmlFile->file);
            $zip->addFromString('hash.txt', $findXmlFile->hash);
            $zip->addFromString('sign.sig', $findXmlFile->sign);


            $zip->close();
            $response = new BinaryFileResponse($zipFile);
            return $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $archiveName . '.zip');
        }
        return new BinaryFileResponse($bid_id, Response::HTTP_NOT_FOUND);
    }


}