<?php
declare(strict_types=1);
namespace App\Controller\Profile;


use App\Model\User\UseCase\User\Email;
use App\ReadModel\Profile\ProfileFetcher;
use App\Security\Voter\ProfileAccess;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EmailController
 * @package App\Controller\Profile
 */
class EmailController extends AbstractController
{
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

    /**
     * EmailController constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param ProfileFetcher $profileFetcher
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, ProfileFetcher $profileFetcher)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->profileFetcher = $profileFetcher;
    }

    /**
     * @param Request $request
     * @param Email\Request\Handler $handler
     * @return Response
     * @Route("/profile/{profile_id}/change/email", name="profile.email")
     */
    public function request(Request $request, string $profile_id, Email\Request\Handler $handler): Response{
        $this->denyAccessUnlessGranted(
            ProfileAccess::PROFILE_INDEX,
            $profile = $this->profileFetcher->find($profile_id)
        );

        $command = new Email\Request\Command($this->getUser()->getId(), $request->getClientIp());
        $form = $this->createForm(Email\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                $this->addFlash('success',  $this->translator->trans('Check your email.',[],'exceptions'));
                return $this->redirectToRoute('profile');
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception'=>$e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/profile/email.html.twig',[
            'form' => $form->createView(),
            'profile' => $profile
        ]);
    }

    /**
     * @param string $token
     * @param string $profile_id
     * @param Email\Confirm\Handler $handler
     * @return Response
     * @Route("/profile/change/email/{token}", name="profile.email.confirm")
     */
    public function confirm(string $token, Email\Confirm\Handler $handler): Response{
        $command = new Email\Confirm\Command($this->getUser()->getId(), $token);
        try{
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Email is successfully changed.',[],'exceptions'));
        }catch (\DomainException $e){
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
        }

        return $this->redirectToRoute('profile');

    }
}
