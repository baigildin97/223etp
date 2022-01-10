<?php
declare(strict_types=1);
namespace App\Controller\Procedure;

use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Procedure\XmlDocument\History\Filter;
use App\ReadModel\Procedure\XmlDocument\History\HistoryFetcher;
use App\ReadModel\Procedure\XmlDocument\XmlDocumentFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Model\Work\Procedure\UseCase\Moderator\Action\{ApproveHandler,
    Command as CommandCause,
    Form as FormCause,
    RejectHandler
};
/**
 * Class HistoryController
 * @package App\Controller\Procedure\History
 */
class HistoryController extends AbstractController
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
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;

    /**
     * @var ProcedureFetcher
     */
    private $procedureFetcher;

    /**
     * @var HistoryFetcher
     */
    private $historyFetcher;

    /**
     * HistoryController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @param HistoryFetcher $historyFetcher
     */
    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger,
        XmlDocumentFetcher $xmlDocumentFetcher,
        ProcedureFetcher $procedureFetcher,
        HistoryFetcher $historyFetcher
    ) {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
        $this->procedureFetcher = $procedureFetcher;
        $this->historyFetcher = $historyFetcher;
    }

    /**
     * @param Request $request
     * @param string $procedure_id
     * @param string $notification_id
     * @return Response
     * @Route("/procedure/{procedure_id}/notification/{notification_id}/history", name="procedure.notification.history")
     */
    public function index(Request $request, string $procedure_id, string $notification_id): Response{
        $procedure = $this->procedureFetcher->findDetail($procedure_id);
        $xmlDocument = $this->xmlDocumentFetcher->findDetailXmlFile($notification_id);


        if ($this->isGranted('ROLE_MODERATOR')){
            $filter = Filter::fromXmlDocument($notification_id);
        } else {
            $filter = Filter::fromXmlDocumentParticipant($notification_id, $procedure->organizer_email);
        }
        $pagination = $this->historyFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );



        return $this->render('app/procedures/notifications/history/index.html.twig', [
            'pagination' => $pagination,
            'xmlDocument' => $xmlDocument,
            'procedure' => $procedure
        ]);
    }
}