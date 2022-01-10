<?php
declare(strict_types=1);
namespace App\Controller\Procedure\Lot;


use App\ReadModel\Procedure\Document\DocumentFetcher;
use App\ReadModel\Procedure\History\DetailView;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Procedure\XmlDocument\XmlDocumentFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HistoryController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;

    /**
     * HistoryController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     */
    public function __construct(TranslatorInterface $translator, LoggerInterface $logger, XmlDocumentFetcher $xmlDocumentFetcher){
        $this->translator = $translator;
        $this->logger = $logger;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
    }

    /**
     * @param Request $request
     * @param string $lotId
     * @param string $historyId
     * @param LotFetcher $lotFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @param SerializerInterface $serializer
     * @param DocumentFetcher $documentFetcher
     * @return Response
     * @Route("/lot/{lotId}/history/{historyId}/show", name="lot.history.show")
     */
    public function show(Request $request, string $historyId, string $lotId, LotFetcher $lotFetcher, ProcedureFetcher $procedureFetcher, SerializerInterface $serializer, DocumentFetcher $documentFetcher): Response{
        $lot = $lotFetcher->findDetail($lotId);
        if($lot === null){
            throw new \DomainException('Page Not Found');
        }

        $findXmlFile = $this->xmlDocumentFetcher->findDetailXmlFile($historyId);
        if($findXmlFile === null){
            throw new \DomainException('Page Not Found');
        }

        $xmlFile = $serializer->deserialize($findXmlFile->file, DetailView::class, 'xml');
        $procedure = $procedureFetcher->findDetail($lot->procedure_id);
        return $this->render('app/procedures/lot/history/show.html.twig',[
            'lot' => $xmlFile->lots[$lot->id_number],
            'procedure' => $procedure,
            'documents' => $xmlFile->lots[$lot->id_number]['documents']
        ]);

    }

}