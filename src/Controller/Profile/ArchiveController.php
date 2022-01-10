<?php
declare(strict_types=1);
namespace App\Controller\Profile;

use App\Model\User\UseCase\Profile\Archive\Handler;
use App\ReadModel\Profile\Filter\Filter;
use App\ReadModel\Profile\Filter\Form;
use App\ReadModel\Profile\ProfileFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ArchiveController
 * @package App\Controller\Profile
 */
class ArchiveController extends AbstractController
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
     * ArchiveController constructor.
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(TranslatorInterface $translator, LoggerInterface $logger) {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @Route("/profile/archive", name="profile.archive")
     */
    public function index(Request $request, ProfileFetcher $profileFetcher): Response {

        $filter = new Filter();
        $form = $this->createForm(Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $profileFetcher->allArchived(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/profile/archive.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }


    /**
     * @param Request $request
     * @param string $profileId
     * @param Handler $handler
     * @return Response
     * @Route("/profiles/{profileId}/archivated", name="profiles.archivated", methods={"POST"})
     */
    public function archived(Request $request, string $profileId, Handler $handler): Response {
//        $command = new Command($profileId);
//        try{
//            $handler->handle($command);
//            $this->addFlash('success', $this->translator->trans('Profile archived successfully.',[],'exceptions'));
//            return $this->redirectToRoute('profiles');
//        }catch (\DomainException $exception){
//            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
//            $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
//            return $this->redirectToRoute('profiles');
//        }
    }

}