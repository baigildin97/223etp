<?php
declare(strict_types=1);

namespace App\Controller\Admin\News;

use App\Model\Admin\Entity\News\Status;
use App\Model\Admin\UseCase\News\Create\Command;
use App\Model\Admin\UseCase\News\Create\Form;
use App\Model\Admin\UseCase\News\Create\Handler;
use App\ReadModel\Admin\News\Filter\Filter;
use App\ReadModel\Admin\News\NewsFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class IndexController
 * @package App\Controller\Admin\News
 * @IsGranted("ROLE_MODERATOR")
 */
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
     * @param NewsFetcher $newsFetcher
     * @return Response
     * @Route("/admin/news", name="admin.news")
     */
    public function index(Request $request, NewsFetcher $newsFetcher): Response {
        $filter = Filter::fromStatus(Status::active()->getName());
        $pagination = $newsFetcher->all($filter, $request->query->getInt('page', 1), self::PER_PAGE);

        return $this->render('app/admin/news/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @param Request $request
     * @param NewsFetcher $newsFetcher
     * @return Response
     * @Route("/admin/news/archived", name="admin.news.archived")
     */
    public function archived(Request $request, NewsFetcher $newsFetcher): Response {
        $filter = Filter::fromStatus(Status::archived()->getName());

        $pagination = $newsFetcher->all($filter, $request->query->getInt('page', 1), self::PER_PAGE);

        return $this->render('app/admin/news/archived.html.twig', [
            'pagination' => $pagination
        ]);
    }



    /**
     * @param Request $request
     * @param Handler $handler
     * @return Response
     * @Route("/admin/news/create", name="admin.news.create")
     */
    public function create(Request $request, Handler $handler): Response{

        $form = $this->createForm(Form::class, $command = new Command());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('News added successfully.',[],'exceptions'));
                return $this->redirectToRoute('admin.news');
            }catch (\DomainException $exception){
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
            }
        }
        return $this->render('app/admin/news/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $news_id
     * @param NewsFetcher $newsFetcher
     * @param \App\Model\Admin\UseCase\News\Edit\Handler $handler
     * @return RedirectResponse|Response
     * @Route("/admin/news/{news_id}/edit", name="admin.news.edit")
     */
    public function edit(
        Request $request,
        string $news_id,
        NewsFetcher $newsFetcher,
        \App\Model\Admin\UseCase\News\Edit\Handler $handler)
    {
        $newsDetail = $newsFetcher->findDetail($news_id);
        $command = \App\Model\Admin\UseCase\News\Edit\Command::edit(
            $newsDetail->id,
            $newsDetail->subject,
            $newsDetail->text
        );

        $form = $this->createForm(\App\Model\Admin\UseCase\News\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('News edit successfully.',[],'exceptions'));
                return $this->redirectToRoute('admin.news');
            }catch (\DomainException $exception){
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
            }
        }
        return $this->render('app/admin/news/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $news_id
     * @param \App\Model\Admin\UseCase\News\Remove\Handler $handler
     * @return RedirectResponse
     * @Route("/admin/news/{news_id}/delete", name="admin.news.delete")
     */
    public function delete(Request $request, string $news_id, \App\Model\Admin\UseCase\News\Remove\Handler $handler): Response{
        try{
            $handler->handle(new \App\Model\Admin\UseCase\News\Remove\Command($news_id));
            $this->addFlash('success', $this->translator->trans('News archived successfully.',[],'exceptions'));
            return $this->redirectToRoute('admin.news');
        }catch (\DomainException $exception){
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
        }
    }
}