<?php
namespace App\Controller;


use App\Model\Flusher;
use App\Model\Front\Entity\OldProcedure\Document\Id;
use App\Model\Front\Entity\OldProcedure\OldProcedureRepository;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{

    private $flusher;

    private $procedureRepository;


    public function __construct(Flusher $flusher, OldProcedureRepository $procedureRepository)
    {
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
    }


    /**
     * @param Client $client
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @Route("/cp/test", name="cp.test")
     */
    public function test(): Response
    {
        $client = new Client();
        $req = $client->get('https://test.229etp.ru/api/procedures');
        $res = json_decode($req->getBody()->getContents(), true);


        foreach ($res['data'] as $re) {
            $procedure = $this->procedureRepository->getIdNumber($re['number']);

            if ($procedure->getDocuments()->isEmpty()){
                foreach ($re['documents'] as $r) {
                    $procedure->addDocument(Id::next(), '1', $r['name'], $r['url']);
                }
            }

            if ($procedure->getNotice()->isEmpty()){
                foreach ($re['notifications'] as $r) {
                    $procedure->addNotice(\App\Model\Front\Entity\OldProcedure\Notice\Id::next(), $r['type'],$r['content']);
                }
            }
            if ($procedure->getProtocols()->isEmpty()){
                foreach ($re['protocols'] as $r) {
                    $procedure->addProtocol(\App\Model\Front\Entity\OldProcedure\Protocols\Id::next(),  $r['name'],$r['content']);
                }
            }
            $this->flusher->flush();
        }

        return new Response('ok');
    }
}