<?php
namespace App\Controller\Admin\Moderator;


use App\Model\Admin\UseCase\Users\Certificate\Reset\Command;
use App\Model\Admin\UseCase\Users\Certificate\Reset\Handler;
use App\Model\User\Entity\Profile\Status;
use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use App\ReadModel\User\Filter\Form;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CertificateController extends AbstractController
{
    private const PER_PAGE = 25;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    private $xmlDocumentFetcher;

    /**
     * UserController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param ProfileFetcher $profileFetcher
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     */
    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        ProfileFetcher $profileFetcher,
        XmlDocumentFetcher $xmlDocumentFetcher
    ){
        $this->logger = $logger;
        $this->translator = $translator;
        $this->profileFetcher = $profileFetcher;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
    }

    /**
     * Список сертификатов ожидающие модерацию
     * @param Request $request
     * @param ProfileFetcher $profileFetcher
     * @return Response
     * @Route("/moderate/certificates", name="moderate.certificates")
     */
    public function index(Request $request, ProfileFetcher $profileFetcher): Response
    {
        $filter = \App\ReadModel\Profile\Filter\Filter::fromStatus(Status::replacingEp()->getName());
        $form = $this->createForm(\App\ReadModel\Profile\Filter\Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $profileFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

//        dd($pagination);

        return $this->render('app/admin/moderator/certificate/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }


    /**
     * @Route("/profile/{profile_id}/change-certificate/confirm", name="profile.change.certificate")
     * @param Request $request
     * @param string $profile_id
     */
    public function confirm(Request $request, string $profile_id, Handler $handler){
        $clientProfile = $this->profileFetcher->find($profile_id);

        $form = $this->createForm(
            \App\Model\Admin\UseCase\Users\Certificate\Reset\Form::class,
            $command = new Command($profile_id, $this->getUser()->getId())
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            try{
                if ($form->get('approved')->isClicked()){
                    $handler->handleApprove($command);
                    $this->addFlash('success',  'Замена ЭП пользователя одобрено.');
                    return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
                }

                if ($form->get('reject')->isClicked()){
                    $handler->handleReject($command);
                    $this->addFlash('success',  'Замена ЭП пользователя отклонено.');
                    return $this->redirectToRoute('profile', ['profile_id' => $profile_id]);
                }
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/admin/moderator/certificate/confirm.html.twig', [
            'form' => $form->createView(),
            'profile' => $clientProfile
        ]);
    }
}