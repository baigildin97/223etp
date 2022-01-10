<?php
declare(strict_types=1);
namespace App\Controller\Certificates;


use App\Container\Model\Certificate\CertificateService;
use App\Model\User\UseCase\Certificate\Create;
use App\Model\User\UseCase\Certificate\Archive;
use App\ReadModel\Certificate\CertificateFetcher;
use App\ReadModel\Certificate\Filter\Filter;
use \App\ReadModel\Certificate\Filter\Form;
use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\User\UserFetcher;
use App\Security\Voter\CertificateAccess;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class IndexController
 * @package App\Controller\Certificates
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
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator) {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param string $user_id
     * @param CertificateFetcher $certificateFetcher
     * @return Response
     * @Route("/certificates/{user_id}", name="certificates")
     */
    public function index(Request $request, string $user_id, CertificateFetcher $certificateFetcher): Response {
        $this->denyAccessUnlessGranted(
            CertificateAccess::CERTIFICATE_SHOW,
            $user_id
        );

        $filter = Filter::forUserId($user_id);
        $form = $this->createForm(Form::class, $filter);

        $form->handleRequest($request);

        $pagination = $certificateFetcher->all(
            $filter,
            $request->query->getInt('page',1),
            self::PER_PAGE
        );

        return $this->render('app/certificates/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'user_id' => $user_id
        ]);
    }

    /**
     * @param string $id
     * @param CertificateFetcher $certificateFetcher
     * @return Response
     * @Route("/certificate/{id}/show", name="certificate.show", methods={"GET"})
     */
    public function show(string $id, CertificateFetcher $certificateFetcher): Response {
        $certificate = $certificateFetcher->findDetail($id);

        $this->denyAccessUnlessGranted(
            CertificateAccess::CERTIFICATE_SHOW,
            $certificate->user_id ?? 0
        );


        return $this->render('app/certificates/show.html.twig', [
            'certificate' => $certificate,
            'profile_id' => $this->getUser()->getProfileId(),
            'user_id' => $user_id = $this->getUser()->getId()
        ]);
    }

    /**
     * @param Request $request
     * @param Create\Handler $handler
     * @param UserFetcher $userFetcher
     * @param CertificateService $env
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     * @Route("/certificate/create", name="certificate.create")
     */
    public function create(Request $request, Create\Handler $handler, UserFetcher $userFetcher, CertificateService $env): Response {
        $command = new Create\Command($this->getUser()->getId());
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);
        $user_id = $this->getUser()->getId();
        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                if ($userFetcher->findDetail($user_id)->issetActiveProfile()) {
                    $this->addFlash('success', $this->translator->trans('Certificate added successfully.',[],'exceptions'));
                }else{
                    $this->addFlash('success', $this->translator->trans('The certificate has been successfully added. Next, go to the "Create profile" tab, and fill in the necessary user data',[],'exceptions'));
                }

                return $this->redirectToRoute('certificates', ['user_id' => $user_id]);
            }catch (\DomainException $exception){
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
            }
        }
        return $this->render('app/certificates/create.html.twig',[
            'form' => $form->createView(),
            'form_data_hash' => $env->getHash()
        ]);
    }

    /**
     * @param string $certificateId
     * @param Request $request
     * @param Archive\Handler $handler
     * @return Response
     * @Route("/certificate/{certificateId}/archived", name="certificate.archived", methods={"POST"})
     */
    public function archived(string $certificateId, Request $request, Archive\Handler $handler): Response {
        $command = new Archive\Command($certificateId);
        $user_id = $this->getUser()->getId();
        try{
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Certificate archived successfully.',[],'exceptions'));
            return $this->redirectToRoute('certificates', ['user_id' => $user_id]);
        }catch (\DomainException $exception){
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            $this->addFlash('error',$this->translator->trans($exception->getMessage(),[],'exceptions'));
            return $this->redirectToRoute('certificates');
        }
    }


}