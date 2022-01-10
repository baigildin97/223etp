<?php
namespace App\Controller\Front;


use App\ReadModel\Procedure\Old\DocumentFetcher;
use App\ReadModel\Procedure\Old\NoticeFetcher;
use App\ReadModel\Procedure\Old\ProtocolFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OldTenderController extends AbstractController
{
    private const PER_PAGE = 25;

    /**
     * @param Request $request
     * @param ProcedureFetcher $procedureFetcher
     * @return Response
     * @Route("/tender/old", name="tender.old")
     */
    public function index(Request $request, ProcedureFetcher $procedureFetcher): Response {
        $pagination = $procedureFetcher->allOld(
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('front/tender/old/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @param string $procedure_id
     * @return Response
     * @Route("/tender/old/{procedure_id}", name="tender.old.show")
     */
    public function show(
        string $procedure_id,
        ProcedureFetcher $procedureFetcher,
        ProtocolFetcher $protocolFetcher,
        DocumentFetcher $documentFetcher,
        NoticeFetcher $noticeFetcher
    ): Response {
        $findDetailProcedure = $procedureFetcher->findDetailOld($procedure_id);

        $addr = json_decode($findDetailProcedure->organizer_legal_address, true);
        $a = $addr['zip'].' '.$addr['region'].' '.$addr['street'];

        $documents = $documentFetcher->all(1,100, $procedure_id);
        $protocols = $protocolFetcher->all(1,100, $procedure_id);
        $notice = $noticeFetcher->all(1,100, $procedure_id);

        return $this->render('front/tender/old/show.html.twig', [
            'procedure' => $findDetailProcedure,
            'legal_address' => $a,
            'documents' => $documents,
            'protocols' => $protocols,
            'notice' => $notice
        ]);
    }

    /**
     * @return Response
     * @Route("/tender/notice/{notice_id}", name="tender.notice.show")
     */
    public function notice(string $notice_id, NoticeFetcher $noticeFetcher): Response {
        $notice = $noticeFetcher->findDetail($notice_id);

        return $this->render('front/tender/old/notice.html.twig', [
            'notic' => $notice
        ]);
    }

}