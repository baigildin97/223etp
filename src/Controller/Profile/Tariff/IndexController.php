<?php
declare(strict_types=1);
namespace App\Controller\Profile\Tariff;


use App\Model\User\UseCase\Profile\Tariff\Create;
use App\Model\User\UseCase\Profile\Tariff\Edit;
use App\Model\User\UseCase\Profile\Tariff\Buy;
use App\Model\User\UseCase\Profile\Tariff\Levels\Create\Command;
use App\Model\User\UseCase\Profile\Tariff\Levels\Create\Form;
use App\Model\User\UseCase\Profile\Tariff\Levels\Create\Handler;
use App\ReadModel\Profile\Tariff\TariffFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexController extends AbstractController
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


    public function __construct(LoggerInterface $logger, TranslatorInterface $translator) {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param TariffFetcher $tariffFetcher
     * @return Response
     * @Route("tariffs", name="tariffs")
     */
    public function index(Request $request, TariffFetcher $tariffFetcher): Response {
        $pagination = $tariffFetcher->all(
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/profile/tariff/index.html.twig', [
            'tariffs' => $pagination
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @param Request $request
     * @param Create\Handler $handler
     * @return Response
     * @Route("tariff/create", name="tariff.create")
     */
    public function create(Request $request, Create\Handler $handler): Response {
        $form = $this->createForm(Create\Form::class, $command = new Create\Command());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            try{
                $handler->handle($command);
                $this->addFlash('success',  $this->translator->trans('Tariff added successfully.',[],'exceptions'));
                return $this->redirectToRoute('tariffs');
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/tariff/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @param Request $request
     * @param string $tariff
     * @param Edit\Handler $handler
     * @param TariffFetcher $tariffFetcher
     * @return Response
     * @Route("tariff/{tariff_id}/edit", name="tariff.edit")
     */
    public function edit(Request $request, string $tariff_id, Edit\Handler $handler, TariffFetcher $tariffFetcher): Response {
        $tariff = $tariffFetcher->findDetail($tariff_id);
        $command = Edit\Command::edit(
            $tariff->title,
            $tariff->cost,
            $tariff->period,
            $tariff->status,
            $tariff->id
        );

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $handler->handle($command);
                $this->addFlash('success',  $this->translator->trans('Tariff changed successfully.',[],'exceptions'));
                return $this->redirectToRoute('tariff.show', ['tariff_id' => $tariff_id]);
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/tariff/edit.html.twig', [
            'tariff' => $tariff,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $tariff_id
     * @param TariffFetcher $tariffFetcher
     * @return Response
     * @Route("/tariff/{tariff_id}/show", name="tariff.show")
     */
    public function show(Request $request, string $tariff_id, TariffFetcher $tariffFetcher): Response{
        if (!$tariff = $tariffFetcher->findDetail($tariff_id)){
            $this->addFlash('success',  $this->translator->trans('Tariff not found.',[],'exceptions'));
            return $this->redirectToRoute('tariffs');
        }

        return $this->render('app/profile/tariff/show.html.twig', [
            'tariff' => $tariff
        ]);
    }


    /**
     * @IsGranted("ROLE_MODERATOR")
     * @param Request $request
     * @param string $tariff_id
     * @param TariffFetcher $tariffFetcher
     * @return Response
     * @Route("/tariff/{tariff_id}/levels", name="tariff.levels")
     */
    public function levels(Request $request, string $tariff_id, TariffFetcher $tariffFetcher): Response{
        if (!$tariff = $tariffFetcher->findDetail($tariff_id)){
            $this->addFlash('success',  $this->translator->trans('Tariff not found.',[],'exceptions'));
            return $this->redirectToRoute('tariffs');
        }

       $findLevels = $tariffFetcher->findLevelsByTariffId($tariff_id);

        return $this->render('app/profile/tariff/levels/index.html.twig', [
            'tariff' => $tariff,
            'levels' => $findLevels
        ]);
    }


    /**
     * @IsGranted("ROLE_MODERATOR")
     * @param Request $request
     * @param string $tariff_id
     * @param TariffFetcher $tariffFetcher
     * @param Handler $handler
     * @return Response
     * @Route("/tariff/{tariff_id}/level/create", name="tariff.level.create")
     */
    public function levelCreate(Request $request, string $tariff_id, TariffFetcher $tariffFetcher, Handler $handler): Response{
        if (!$tariff = $tariffFetcher->findDetail($tariff_id)){
            $this->addFlash('success',  $this->translator->trans('Tariff not found.',[],'exceptions'));
            return $this->redirectToRoute('tariffs');
        }

        $command = new Command($tariff_id);

        $form = $this->createForm(Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $handler->handle($command);
                $this->addFlash('success',  $this->translator->trans('Уровень успешно добавлен',[],'exceptions'));
                return $this->redirectToRoute('tariff.levels', ['tariff_id' => $tariff_id]);
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/tariff/levels/create.html.twig', [
            'tariff' => $tariff,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $tariff_id
     * @param Buy\Handler $handler
     * @return Response
     * @throws \Exception
     * @Route("/tariff/{tariff_id}/buy", name="tariff.buy", methods={"POST"})
     */
    public function buy(Request $request, string $tariff_id, Buy\Handler $handler): Response {
        try {
            $handler->handle(
                new Buy\Command(
                    $tariff_id,
                    $this->getUser()->getId()
                )
            );
        }catch (\DomainException $e){
            $this->logger->error($e->getMessage(), ['exception'=>$e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
        }

        return $this->redirectToRoute('tariffs');
    }


}