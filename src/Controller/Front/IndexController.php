<?php
declare(strict_types=1);

namespace App\Controller\Front;


use App\Container\Model\Certificate\CertificateService;
use App\Model\Admin\Entity\News\Status;
use App\Model\Front\UseCase\Contacts\Command;
use App\Model\Front\UseCase\Contacts\Form;
use App\Model\Front\UseCase\Contacts\Handler;
use App\ReadModel\Admin\News\Filter\Filter;
use App\ReadModel\Admin\News\NewsFetcher;
use App\Services\CryptoPro\CryptoPro;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class IndexController
 * @package App\Controller\Front
 */
class IndexController extends AbstractController
{
    private const PER_PAGE = 5;

    private const RECAPTCHA_BOOL = 5;

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
     * @return Response
     * @Route("/", name="front")
     */
    public function index(): Response
    {
        return $this->render('front/home.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/news", name="front.news")
     */
    public function news(Request $request, NewsFetcher $newsFetcher): Response
    {
        $filter = Filter::fromStatus(Status::active()->getName());
        $pagination = $newsFetcher->all($filter, $request->query->getInt('page', 1), self::PER_PAGE);

        return $this->render('front/news/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * @param Request $request
     * @param string $news_id
     * @param NewsFetcher $newsFetcher
     * @return Response
     * @Route("/news/show/{news_id}", name="front.news.show")
     */
    public function show(Request $request, string $news_id, NewsFetcher $newsFetcher): Response
    {
        $news = $newsFetcher->findDetail($news_id);

        return $this->render('front/news/show.html.twig', [
            'news' => $news
        ]);
    }

    /**
     * @param Request $request
     * @param Handler $handler
     * @return Response
     * @Route("/contacts", name="front.contacts")
     */
    public function contacts(Request $request, Handler $handler): Response
    {
        $command = new Command();
        $form = $this->createForm(Form::class, $command);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Message sent successfully.', [], 'exceptions'));
                return $this->redirectToRoute('front.contacts');
            } catch (\DomainException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error', $this->translator->trans($exception->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('front/contacts.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/get_ecp", name="front.get.ecp")
     */
    public function getEcp(Request $request): Response
    {
        return $this->render('front/get_ecp.html.twig');
    }

    /**
     * @param Request $request
     * @param CertificateService $certificateService
     * @return JsonResponse|Response
     * @Route("/verify-sign", name="front.verify.sign")
     */
    public function verifySign(Request $request, CertificateService $certificateService)
    {
        if ($request->isXmlHttpRequest()) {
            $sign = $request->get('sign');
            try {
                CryptoPro::verify($certificateService->getHash(), $sign);
                return new JsonResponse(['answer' => 'ok']);

            } catch (\Exception $e) {
                return new JsonResponse(['answer' => $e->getMessage()]);
            }
        } else {
            return $this->render('front/verify_sign.html.twig', ['hash' => $certificateService->getHash()]);
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/documents", name="front.documents")
     */
    public function documents(Request $request): Response
    {
        return $this->render('front/documents.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/rates", name="front.tariffs")
     */
    public function tariffs(Request $request): Response
    {
        return $this->render('front/tariffs.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/requirement", name="front.requirement")
     */
    public function requirement(Request $request): Response
    {
        return $this->render('front/requirement.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/privacy", name="front.privacy")
     */
    public function privacy(Request $request): Response
    {
        return $this->render('front/privacy.html.twig');
    }
}