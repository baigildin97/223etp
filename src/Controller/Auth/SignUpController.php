<?php
declare(strict_types=1);
namespace App\Controller\Auth;


use App\Model\User\UseCase\User\SignUp;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SignUpController extends AbstractController
{
    private $logger;
    private $translator;
    private $recaptcha3Validator;

    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        Recaptcha3Validator $recaptcha3Validator
    ) {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->recaptcha3Validator = $recaptcha3Validator;
    }

    /**
     * @param Request $request
     * @param SignUp\Request\Handler $handler
     * @return Response
     * @Route("/sign-up",name="auth.sign.up")
     */
    public function request(Request $request, SignUp\Request\Handler $handler): Response {
        $command = new SignUp\Request\Command($request->getClientIp());

        $form = $this->createForm(SignUp\Request\Form::class, $command);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans("A letter has been sent to email %email%. follow the link to confirm your email.", ['%email%' => $command->email], 'exceptions'));
                return $this->redirectToRoute('app_login');
            }catch (\DomainException $e){

                $this->logger->error($e->getMessage(),['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/auth/signup.html.twig',[
            'form' => $form->createView()
        ]);
    }


    /**
     * @param string $token
     * @param SignUp\Confirm\ByToken\Handler $handler
     * @return Response
     * @Route("/sign-confirm/{token}", name="auth.sign.confirm")
     */
    public function confirm(string $token, SignUp\Confirm\ByToken\Handler $handler): Response {
        $command = new SignUp\Confirm\ByToken\Command($token);
        try{
            $handler->handle($command);
            $this->addFlash('success', $this->translator->trans('Email is successfully confirmed.', [], 'exceptions'));
            return $this->redirectToRoute('app_login');
        }catch (\DomainException $e){
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @return Response
     * @Route("/mail/test", name="auth.test.html")
     */
    public function test(): Response {
        return $this->render('mail/test.html.twig');
    }

}