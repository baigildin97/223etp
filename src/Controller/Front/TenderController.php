<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Flusher;
use App\Model\Front\Entity\OldProcedure\Document\Document;
use App\Model\Front\Entity\OldProcedure\Id;
use App\Model\Front\Entity\OldProcedure\OldProcedure;
use App\Model\Front\Entity\OldProcedure\OldProcedureRepository;
use App\Model\Work\Procedure\Entity\Status;
use App\Model\Work\Procedure\Services\Procedure\File\FileHelper;
use App\Model\Work\Procedure\Services\Procedure\Sign\NotifyDetailView;
use App\ReadModel\Procedure\Document\DocumentFetcher;
use App\ReadModel\Procedure\Filter\Filter;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Procedure\XmlDocument\XmlDocumentFetcher;
use App\Security\Voter\ProcedureAccess;
use Doctrine\DBAL\Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TenderController
 * @package App\Controller\Front
 */
class TenderController extends AbstractController
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
     * IndexController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param ProcedureFetcher $procedureFetcher
     * @return Response
     * @Route("/tender", name="front.tender")
     */
    public function tender(Request $request, ProcedureFetcher $procedureFetcher): Response
    {
        $filter = Filter::forStatus(
            [
                Status::STATUS_MODERATE,
                Status::STATUS_MODERATED,
                Status::STATUS_ARCHIVED,
                Status::STATUS_REJECTED,
                Status::STATUS_NEW
            ]
        );


        $form = $this->createForm(\App\ReadModel\Procedure\Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $procedureFetcher->getAllProcedures(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        $pagination_old = $procedureFetcher->allOld(
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('front/tender/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'pagination_old' => $pagination_old
        ]);

    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param DocumentFetcher $documentFetcher
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @param LotFetcher $lotFetcher
     * @return Response
     * @throws Exception
     * @Route("/tender/show/{procedure_id}", name="front.tender.show")
     */
    public function show(Request $request,
                         string $procedure_id,
                         DocumentFetcher $documentFetcher,
                         XmlDocumentFetcher $xmlDocumentFetcher,
                         LotFetcher $lotFetcher): Response
    {
        $findDetailProcedure = $lotFetcher->findDetailByProcedureId($procedure_id);

        $files = FileHelper::rearrangeByType(
            $documentFetcher->getAll($procedure_id),
            $findDetailProcedure
        );

        $filesCount = FileHelper::getFilesCount($files, $findDetailProcedure);


        $pagination = $xmlDocumentFetcher->findApprovedNotification(
            \App\ReadModel\Procedure\XmlDocument\Filter\Filter::fromProcedure($procedure_id),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('front/tender/show.html.twig', [
            'procedure' => $findDetailProcedure,
            'files' => $files,
            'filesCount' => $filesCount,
            'notifications' => $pagination
        ]);

    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $notification_id
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @param SerializerInterface $serializer
     * @param LotFetcher $lotFetcher
     * @return Response
     * @throws Exception
     * @Route("/tender/show/{procedure_id}/notification/{notification_id}", name="front.tender.notification.show")
     */
    public function notification(Request $request,
                                 string $procedure_id,
                                 string $notification_id,
                                 XmlDocumentFetcher $xmlDocumentFetcher,
                                 ProcedureFetcher $procedureFetcher,
                                 SerializerInterface $serializer,
                                 LotFetcher $lotFetcher): Response
    {

        $procedure = $procedureFetcher->findDetail($procedure_id);
        $lot = $lotFetcher->findDetailByProcedureId($procedure_id);

        $xmlDocument = $xmlDocumentFetcher->findDetailXmlFile($notification_id);

        $xml = $serializer->deserialize($xmlDocument->file, NotifyDetailView::class, 'xml');

        return $this->render('front/tender/notification.html.twig', [
            'procedure' => $procedure,
            'lot' => $lot,
            'xml' => $xml,
            'notification' => $xmlDocument
        ]);

    }


    //TODO Import old Procedures

    /**
     * @param Request $request
     * @param Flusher $flusher
     * @param OldProcedureRepository $oldProcedureRepository
     * @Route("/tender/import", name="front.tender.import")
     */
    public function import(Request $request,
                           Flusher $flusher,
                           OldProcedureRepository $oldProcedureRepository)
    {

        $client = new Client([
            'base_uri' => 'http://test.229etp.ru/'
        ]);
        $response = $client->request('GET', 'example');
        $dataJson = $response->getBody();
        dd($dataJson);

        $arrayTestDocumentsProcedures = [];
        $arrayTestDocumentsProcedures[] = [
            'id' => '123',
            'type' => 'DOCUMENT',
            'name' => 'Договор о задатке',
            'path' => '/assets/files/dogovor-o-zadatke.pdf'
        ];


        $arrayTestProtocols = [];
        $arrayTestProtocols[] = [
            'id' => '1',
            'name' => 'Протокол об определение победителя',
            'text' => 'Протокол об определение победителя торгов'
        ];

        $arrayTestProcedures = [];
        $arrayTestProcedures[] = [
            'idNumber' => "123",
            'tenderBasic' => "test",
            'pricePresentationForm' => "test",
            'organizer' => "test",
            'startDateOfApplications' => "09.04.2021",
            'closingDateOfApplications' => "09.04.2021",
            'startTradingDate' => "09.04.2021",
            'status' => "Аукцион завершен",
            'reloadLot' => "Нет",
            'auctionStep' => "1 0000",
            'start_cost' => "50 000",
            'nds' => "Ндс не облагается",
            'deposit_amount' => "5 000",
            'arrestedPropertyType' => "Недвижимое имщуество",
            'additionalObjectCharacteristics' => "test",
            'region' => "Северо-Западный",
            'locationObject' => "Санкт-петербург",
            'organizerShortName' => "ООО «Бизнес-Стиль»",
            'organizerFullName' => "бщество с ограниченной ответственностью «Бизнес-Стиль»",
            'organizerContactFullName' => "Шишков Евгений Николаевич",
            'organizerPhone' => "+79219568640",
            'organizerEmail' => "business-style@bk.ru",
            'organizerLegalAddress' => "198035 Северо-ЗападныйСанкт-Петербург наб. реки Екатерингофки, д.29-31, лит.П, пом.14,15",
            'debtorFullName' => "Тумайкина А.С.",
            'procedure_info_applications' => "test",
            'procedure_info_place' => "test",
            'procedure_info_location' => "test",
            'lotName' => "test",
            'textNotification' => "test",
            'documents' => $arrayTestDocumentsProcedures,
            'protocols' => $arrayTestProtocols,
        ];

       
        
        foreach ($arrayTestProcedures as $row){
            $procedure = new OldProcedure(
                Id::next(),
                $row['idNumber'],
                $row['tenderBasic'],
                $row['pricePresentationForm'],
                $row['organizer'],
                $row['startDateOfApplications'],
                $row['closingDateOfApplications'],
                $row['startTradingDate'],
                $row['status'],
                $row['reloadLot'],
                $row['auctionStep'],
                $row['start_cost'],
                $row['nds'],
                $row['deposit_amount'],
                $row['arrestedPropertyType'],
                $row['additionalObjectCharacteristics'],
                $row['region'],
                $row['locationObject'],
                $row['organizerShortName'],
                $row['organizerFullName'],
                $row['organizerContactFullName'],
                $row['organizerPhone'],
                $row['organizerEmail'],
                $row['organizerLegalAddress'],
                $row['debtorFullName'],
                $row['procedure_info_applications'],
                $row['procedure_info_place'],
                $row['procedure_info_location'],
                $row['lotName'],
                $row['textNotification']
            );

            $oldProcedureRepository->add($procedure);

            //Push documents
            foreach ($row['documents'] as $document){
                $procedure->addDocument(
                    \App\Model\Front\Entity\OldProcedure\Document\Id::next(),
                    $document['type'],
                    $document['name'],
                    $document['path']

                );
            }

            // Push protocols
            foreach ($row['protocols'] as $protocol){
                $procedure->addProtocol(
                    \App\Model\Front\Entity\OldProcedure\Protocols\Id::next(),
                    $protocol['name'],
                    $protocol['text']
                );
            }

    /*        //Push notice
            $procedure->addNotice(
                \App\Model\Front\Entity\OldProcedure\Notice\Id::next(),
                'Извещение о проведение торгов',
                'Извещение о проведение торгов'
            );*/





            $flusher->flush();

        }

    }
}