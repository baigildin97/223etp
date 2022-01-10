<?php
declare(strict_types=1);
namespace App\Controller\Auth;


use App\Container\Model\Certificate\CertificateService;
use App\Model\User\Entity\Certificate\Certificate;
use App\Model\User\UseCase\Certificate\Reset\Request\Command;
use App\Model\User\UseCase\Certificate\Reset\Request\Form;
use App\Model\User\UseCase\Certificate\Reset\Request\Handler;
use App\Model\User\UseCase\User\Reset;
use App\ReadModel\User\UserFetcher;
use App\Services\CryptoPro\CryptoPro;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetController extends AbstractController
{

    private $logger;
    private $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Route("/test", name="mail.test")
     */
    public function mailTest(){
        return $this->render('mail/user/reset-password.html.twig');
    }


    /**
     * @param Request $request
     * @param Reset\Request\Handler $handler
     * @return Response
     * @Route("/password-reset", name="auth.reset.request")
     */
    public function request(Request $request, Reset\Request\Handler $handler): Response {
        $command = new Reset\Request\Command();

        $form = $this->createForm(Reset\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try{
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Check your email.',[],'exceptions'));
                return $this->redirectToRoute('app_login');
            }catch (\DomainException $e){
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            }
        }

        return $this->render('app/auth/reset/request.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @param string $token
     * @param Request $request
     * @param Reset\Reset\Handler $handler
     * @return Response
     * @Route("/password-reset-confirm/{token}", name="auth.reset.confirm")
     */
    public function confirm(string $token, Request $request, Reset\Reset\Handler $handler): Response
    {
        $command = new Reset\Reset\Command($token);

        $form = $this->createForm(Reset\Reset\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', $this->translator->trans('Password is successfully changed.', [], 'exceptions'));
                return $this->redirectToRoute('app_login');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/auth/reset/reset.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Handler $handler
     * @param CertificateService $env
     * @return Response
     * @Route("/certificate-reset", name="cert.reset.request")
     */
    public function requestCrypto(Request $request, Handler $handler, CertificateService $env): Response
    {
        $command = new Command($env->getHash());

        $form = $this->createForm(Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Запрос на смену ЭЦП успешно создан.
                 Ссылка для подтверждения была отправлена на Вашу электронную почту.');
                return $this->redirect($this->generateUrl('app_login_crypt'));
            } catch (\Exception $e)
            {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
            }
        }

        return $this->render('app/auth/reset/certReset.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string $token
     * @param \App\Model\User\UseCase\Certificate\Reset\Confirm\Handler $handler
     * @return Response
     * @Route("certificate-reset-confirm/{token}", name="cert.reset.confirm")
     */
    public function confirmCrypto(Request $request, string $token,
                                  \App\Model\User\UseCase\Certificate\Reset\Confirm\Handler $handler): Response
    {
        $command = new \App\Model\User\UseCase\Certificate\Reset\Confirm\Command($token);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Ваша ЭЦП успешно изменена');
            return $this->redirect($this->generateUrl('app_login_crypt'));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $this->translator->trans($e->getMessage(),[],'exceptions'));
            return $this->redirect($this->generateUrl('cert.reset.request'));
        }
    }
}