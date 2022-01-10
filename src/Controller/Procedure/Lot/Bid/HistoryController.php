<?php
declare(strict_types=1);
namespace App\Controller\Procedure\Lot\Bid;

use App\Model\Admin\Entity\Settings\Key;
use App\Model\Work\Procedure\Services\Bid\Sign\SignXmlGenerator;
use App\Model\Work\Procedure\Services\Bid\Sign\XmlDetailView;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Procedure\Bid\Document\DocumentFetcher;
use App\ReadModel\Procedure\Bid\History\DetailView;
use App\ReadModel\Procedure\Bid\XmlDocument\Filter\Filter;
use App\ReadModel\Procedure\Bid\XmlDocument\XmlDocumentFetcher;
use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use App\ReadModel\Profile\ProfileFetcher;
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
     * @var BidFetcher
     */
    private $bidFetcher;

    /**
     * HistoryController constructor.
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, BidFetcher $bidFetcher, XmlDocumentFetcher $xmlDocumentFetcher) {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
        $this->bidFetcher = $bidFetcher;
    }


    /**
     * @param Request $request
     * @param string $bidId
     * @return Response
     * @Route("/bid/{bidId}/history", name="bid.history")
     */
    public function index(Request $request, string $bidId): Response {
        $bid = $this->bidFetcher->findDetail($bidId);
        $filter = Filter::fromBid($bid->id);
        $bidHistory = $this->xmlDocumentFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/procedures/lot/bid/history/index.html.twig', [
            'bid_history' => $bidHistory,
            'bid' => $bid
        ]);
    }

    /**
     * @param Request $request
     * @param string $bidId
     * @param string $historyId
     * @param SerializerInterface $serializer
     * @return Response
     * @Route("/bid/{bidId}/history/{historyId}/show", name="bid.history.show")
     */
    public function show(Request $request, string $bidId, string $historyId, SerializerInterface $serializer): Response {
        $bid = $this->bidFetcher->findDetail($bidId);
        if($bid === null){
            throw new \DomainException('Page Not Found');
        }

        $findXmlFile = $this->xmlDocumentFetcher->findDetailXmlFile($historyId);
        if($findXmlFile === null){
            throw new \DomainException('Page Not Found');
        }
        $xmlFile = $serializer->deserialize($findXmlFile->file, XmlDetailView::class, 'xml');



        return $this->render('app/procedures/lot/bid/history/show.html.twig',[
            'bid' => $xmlFile,
            'bid_original' => $bid,
            'documents' => $xmlFile->documents,
            'history_id' => $historyId,
        ]);
    }

    /**
     * @param Request $request
     * @param string $bidId
     * @param string $historyId
     * @param SerializerInterface $serializer
     * @param SignXmlGenerator $signXmlGenerator
     * @param ProfileFetcher $profileFetcher
     * @param RequisiteFetcher $requisiteFetcher
     * @param SettingsFetcher $settingsFetcher
     * @param DocumentFetcher $documentFetcher
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     * @Route("/bid/{bidId}/ajax-load", name="bid.history.ajax_load")
     */
    public function ajaxLoad(
        Request $request,
        string $bidId,
        SerializerInterface $serializer,
        SignXmlGenerator $signXmlGenerator,
        ProfileFetcher $profileFetcher,
        RequisiteFetcher $requisiteFetcher,
        SettingsFetcher $settingsFetcher,
        DocumentFetcher $documentFetcher
    ): Response {
        $bid = $this->bidFetcher->findDetail($bidId);
        if($bid === null){
            throw new \DomainException('Page Not Found');
        }

        if ($this->isGranted('ROLE_MODERATOR'))
            $profile = $profileFetcher->find($bid->participant_id);
        else
            $profile = $profileFetcher->findDetailByUserId($this->getUser()->getId());

        $findRequisit = $requisiteFetcher->findDetail($bid->requisite_id);
        $xml = $signXmlGenerator->generate($bid,$profile,$findRequisit);
        $findNameOrganization = $settingsFetcher->findDetailByKey(Key::nameOrganization());
        $findServerName = $settingsFetcher->findDetailByKey(Key::siteDomain());
        $xmlDeserialize = $serializer->deserialize($xml, XmlDetailView::class, 'xml');
        $documents = $documentFetcher->all(
            \App\ReadModel\Procedure\Bid\Document\Filter\Filter::fromBid($bid->id),
            $request->query->getInt('page', 1),
            1000
        );

        return $this->render('app/procedures/lot/bid/history/ajax_load.html.twig',[
            'bid' => $bid,
            'xmlDeserialize' => $xmlDeserialize,
            'request' => $findRequisit,
            'documents' => $documents,
            'profile' => $profile,
            'server_name' => $findServerName,
            'name_organization' => $findNameOrganization
        ]);
    }
}
