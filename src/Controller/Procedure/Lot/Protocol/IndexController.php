<?php
declare(strict_types=1);

namespace App\Controller\Procedure\Lot\Protocol;

use App\Container\Model\User\Service\ProtocolGeneratorFactory;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Protocol\Type;
use App\Model\Work\Procedure\UseCase\Protocol\Create\Command;
use App\Model\Work\Procedure\UseCase\Protocol\Create\Form;
use App\Model\Work\Procedure\UseCase\Protocol\Sign\Organizer\Handler;
use App\ReadModel\Certificate\CertificateFetcher;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Procedure\Lot\Protocol\Filter\Filter;
use App\ReadModel\Procedure\Lot\Protocol\ProtocolFetcher;
use App\ReadModel\Procedure\Lot\Protocol\Xml\DetailView;
use App\ReadModel\Profile\ProfileFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zend\EventManager\Exception\DomainException;

/**
 * Class IndexController
 * @package App\Controller\Procedure\Lot\Protocol
 */
class IndexController extends AbstractController
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
     * @param string $lot_id
     * @param ProtocolFetcher $protocolFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @param LotFetcher $lotFetcher
     * @return Response
     * @Route("/lot/{lot_id}/protocols", name="lot.protocols")
     */
    public function index(Request $request,
                          string $lot_id,
                          ProtocolFetcher $protocolFetcher,
                          ProcedureFetcher $procedureFetcher,
                          LotFetcher $lotFetcher
    ): Response
    {
        $lot = $lotFetcher->findDetail($lot_id);
        $filter = Filter::fromProcedure($lot->procedure_id);
        $pagination = $protocolFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('app/procedures/lot/protocol/index.html.twig', [
            'protocols' => $pagination,
            'lot' => $lot,
            'procedure' => $procedureFetcher->findDetail($lot->procedure_id)
        ]);
    }

    /**
     * @param Request $request
     * @param string $lot_id
     * @param LotFetcher $lotFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @return Response
     * @Route("/lot/{lot_id}/protocol/create", name="lot.protocol.create")
     */
    public function create(Request $request, string $lot_id, LotFetcher $lotFetcher, ProcedureFetcher $procedureFetcher): Response
    {
        $form = $this->createForm(Form::class, $command = new Command());
        $lot = $lotFetcher->findDetail($lot_id);
        $procedure = $procedureFetcher->findDetail($lot->procedure_id);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $type = new Type($command->protocolType);
                if ($type->isResultProtocol() or $type->isWinnerProtocol()) {
                    if (!$lot->isWinner()) {
                    //    throw new \DomainException("Торги не состоялись, либо еще не проведены для формирование данного протокола.");
                    }

                    return $this->redirectToRoute('lot.protocol.requisite', ['lot_id' => $lot_id, 'protocol_type' => $type->getName()]);
                }


                return $this->redirectToRoute('lot.protocol.generate', ['lot_id' => $lot_id, 'protocol_type' => $type->getName()]);
            } catch (\DomainException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error', $this->translator->trans($exception->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/lot/protocol/switch.html.twig', [
            'form' => $form->createView(),
            'lot' => $lot,
            'procedure' => $procedure
        ]);
    }

    /**
     * @param Request $request
     * @param string $lot_id
     * @param string $protocol_type
     * @param LotFetcher $lotFetcher
     * @param ProfileFetcher $profileFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @return Response
     * @Route("/lot/{lot_id}/protocol/{protocol_type}/requisite", name="lot.protocol.requisite")
     */
    public function selectRequisite(Request $request,
                                    string $lot_id,
                                    string $protocol_type,
                                    LotFetcher $lotFetcher,
                                    ProfileFetcher $profileFetcher,
                                    ProcedureFetcher $procedureFetcher): Response
    {
        $lot = $lotFetcher->findDetail($lot_id);
        $procedure = $procedureFetcher->findDetail($lot->procedure_id);
        $profile = $profileFetcher->findDetailByUserId($user_id = $this->getUser()->getId());
        $type = new Type($protocol_type);

        $form = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Protocol\Requisite\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Protocol\Requisite\Command(),
            ['payment_id' => $profile->payment_id],
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try{


                return $this->redirectToRoute('lot.protocol.generate', [
                    'lot_id' => $lot_id,
                    'protocol_type' => $type->getName()
                    ]);
            } catch (\DomainException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error', $this->translator->trans($exception->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/procedures/lot/protocol/requisite.html.twig', [
            'lot' => $lot,
            'type'=> $type->getName(),
            'procedure' => $procedure,
            'procedure_id' => $lot->procedure_id,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $lot_id
     * @param string $protocol_type
     * @param LotFetcher $lotFetcher
     * @param ProcedureFetcher $procedureFetcher
     * @return Response
     * @Route("/lot/{lot_id}/protocol/{protocol_type}/generate", name="lot.protocol.generate")
     */
    public function generate(Request $request, string $lot_id, string $protocol_type, LotFetcher $lotFetcher, ProcedureFetcher $procedureFetcher): Response
    {
        $type = new Type($protocol_type);
        if (!$type->isCancellationProtocolResult()) {
            return $this->redirectToRoute('lot.protocol.sign', ['lot_id' => $lot_id, 'protocol_type' => $protocol_type, 'organizer_comment' => 'false']);
        }


        $form = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Protocol\Create\Generate\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Protocol\Create\Generate\Command()
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('lot.protocol.sign', [
                'lot_id' => $lot_id,
                'protocol_type' => $protocol_type,
                'organizer_comment' => $command->organizerComment
            ]);
        }

        $lot = $lotFetcher->findDetail($lot_id);
        $procedure = $procedureFetcher->findDetail($lot->procedure_id);

        return $this->render('app/procedures/lot/protocol/create.html.twig', [
            'lot' => $lot,
            'procedure' => $procedure,
            'type' => $type->getName(),
            'typeName' => $type->getLocalizedName(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $lot_id
     * @param string $protocol_id
     * @param ProfileFetcher $profileFetcher
     * @param LotFetcher $lotFetcher
     * @param CertificateFetcher $certificateFetcher
     * @param ProtocolFetcher $protocolFetcher
     * @param \App\Model\Work\Procedure\UseCase\Protocol\Sign\Winner\Handler $handler
     * @param SerializerInterface $serializer
     * @return Response
     * @Route("/lot/{lot_id}/protocol/{protocol_id}", name="lot.protocol.show")
     */
    public function show(Request $request, string $lot_id, string $protocol_id,
                         ProfileFetcher $profileFetcher,
                         LotFetcher $lotFetcher,
                         CertificateFetcher $certificateFetcher,
                         ProtocolFetcher $protocolFetcher,
                         \App\Model\Work\Procedure\UseCase\Protocol\Sign\Winner\Handler $handler,
                         SerializerInterface $serializer): Response
    {
        $lot = $lotFetcher->findDetail($lot_id);
        $protocol = $protocolFetcher->findDetail($protocol_id);

        $certificateOrganizer = $certificateFetcher->findDetailByThumbprint($protocol->xml_certificate_thumbprint_organizer);
        $certificateParticipant = $certificateFetcher->findDetailByThumbprint($protocol->xml_certificate_thumbprint_participant);

        $protocolType = new Type($protocol->type);

        $deserialize = $serializer->deserialize($protocol->xml_file, DetailView::class, 'xml');

        $form = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Protocol\Sign\Winner\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Protocol\Sign\Winner\Command(
                $this->getUser()->getId(),
                $protocol->id,
                $lot->procedure_id,
                $lot->id,
                $protocol->xml_hash_organizer
            )
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('lot.protocols', ['lot_id' => $lot->id]);
            } catch (\DomainException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error', $this->translator->trans($exception->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render("app/procedures/lot/protocol/{$protocolType->getName()}/show.html.twig", [
            'deserialize' => $deserialize,
            'lot' => $lot,
            'hash' => $protocol->xml_hash_organizer,
            'protocol' => $protocol,
            'certificateOrganizer' => $certificateOrganizer,
            'certificateParticipant' => $certificateParticipant,
            'form' => $form->createView(),
            'certificate_thumbprint' => $profileFetcher->getCertificateThumbprint($this->getUser()->getId()),
        ]);
    }

    /**
     * @param Request $request
     * @param string $lot_id
     * @param string $protocol_type
     * @param string|null $organizer_comment
     * @param ProtocolGeneratorFactory $protocolGeneratorFactory
     * @param SerializerInterface $serializer
     * @param ProfileFetcher $profileFetcher
     * @param LotFetcher $lotFetcher
     * @param Handler $handler
     * @return Response
     * @Route("/lot/{lot_id}/protocol/{protocol_type}/sign/{organizer_comment}", name="lot.protocol.sign")
     */
    public function sign(
        Request $request,
        string $lot_id,
        string $protocol_type,
        ?string $organizer_comment,
        ProtocolGeneratorFactory $protocolGeneratorFactory,
        SerializerInterface $serializer,
        ProfileFetcher $profileFetcher,
        LotFetcher $lotFetcher,
        Handler $handler
    ): Response
    {
        $type = new Type($protocol_type);

        $requisiteId = null;

        if ($type->isResultProtocol()){
            $getPostData = $request->request->get('form');
            $requisiteId = $getPostData['requisite'];
        }

        $lot = $lotFetcher->findDetail($lot_id);
        $generator = $protocolGeneratorFactory->get($typeProtocol = new Type($protocol_type));
        $xml = $generator->generate(new Id($lot->procedure_id), $organizer_comment, $requisiteId);

        $deserialize = $serializer->deserialize($xml->content, DetailView::class, 'xml');

        $form = $this->createForm(
            \App\Model\Work\Procedure\UseCase\Protocol\Sign\Organizer\Form::class,
            $command = new \App\Model\Work\Procedure\UseCase\Protocol\Sign\Organizer\Command(
                $this->getUser()->getId(),
                $lot->procedure_id,
                $lot->id,
                $typeProtocol->getName(),
                $xml->nextStatus,
                $xml->content,
                $xml->hash
            )
        , ['requisite_id' => $requisiteId]);


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                return $this->redirectToRoute('lot.protocols', ['lot_id'=>$lot->id]);
            }catch (\DomainException $exception){
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
            }
        }

        return $this->render("app/procedures/lot/protocol/{$typeProtocol->getName()}/sign.html.twig", [
            'protocol' => $deserialize,
            'procedure_id' => $lot->procedure_id,
            'lot' => $lot,
            'hash' => $xml->hash,
            'certificate_thumbprint' => $profileFetcher->getCertificateThumbprint($this->getUser()->getId()),
            'form' => $form->createView()
        ]);
    }

}