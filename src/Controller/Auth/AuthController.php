<?php

namespace App\Controller\Auth;

use App\Container\Model\Certificate\CertificateService;
use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHashGenerator;
use App\Model\User\Service\TokenGenerator;
use App\Model\User\UseCase\User\SignUp\Request\Message;
use App\Services\Tasks\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    private $emailTokenGenerator;
    private $userRepository;
    private $flusher;
    private $notificationService;
    private $router;

    public function __construct(TokenGenerator $emailTokenGenerator,
                                UserRepository $userRepository,
                                Flusher $flusher,
                                Notification $notificationService,
                                RouterInterface $router)
    {
        $this->emailTokenGenerator = $emailTokenGenerator;
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->notificationService = $notificationService;
        $this->router = $router;
    }

    /**
     * @Route("/login", name="app_login")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return RedirectResponse|Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, CertificateService $env)//: Response
    {
        // $2y$10$mKa/nUJxac/7/CoEw7rRkeAFR34mzO4vRDSxe27V8ETGCFAR.lJ0e
        // 123456
        // $2y$10$.PZK8BE7o6Lwga2AxdLuue3ob8.GJj48MLxivroE27un/MIxQkd6y
        //
        // dd($passwordHashGenerator->hash('123456'));

        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->isXmlHttpRequest()){
            return $this->redirect($this->generateUrl('app_login'), 302);
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error instanceof DisabledException)
            if (!$this->checkUserConfirmationStatus($error->getUser()->getId()))
                return $this->redirect($this->generateUrl('app_login'));

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('app/auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'captcha_key' => $env->getCaptchaPubKey()
        ]);
    }

    /**
     * @Route("/login_crypt", name="app_login_crypt")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @param Dotenv $env
     * @return RedirectResponse|Response
     */
    public function loginCrypt(Request $request, AuthenticationUtils $authenticationUtils, CertificateService $env)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->isXmlHttpRequest()){
            return $this->redirect($this->generateUrl('app_login'), 302);
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error instanceof DisabledException)
            if (!$this->checkUserConfirmationStatus($error->getUser()->getId()))
                return $this->redirect($this->generateUrl('app_login'));

        return $this->render('app/auth/loginCrypt.html.twig', ['error' => $error, 'crypt_login_hash' => $env->getHash()]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    private function checkUserConfirmationStatus(string $userId): bool
    {
        $user = $this->userRepository->get(new Id($userId));
        $token = $this->emailTokenGenerator->generate();

        if ($user->getStatus()->isWait()) {
            if ($user->updateConfirmToken($token, $date = new \DateTimeImmutable())) {
                $confirmUrl = $this->router->getContext()->getScheme() . '://' .
                    $this->router->getContext()->getHost() .
                    $this->router->generate('auth.sign.confirm', ['token' => $token]);
                $message = Message::confirmSignUp($user->getEmail(), 'РесТорг', $confirmUrl);

                $this->notificationService->emailToOneUser($message);
                $this->flusher->flush();
            }

            $this->addFlash('error', "Ваша учетная запись
             не активирована. Письмо для активации было отправлено Вам на email. 
             До возможности повторной отправки письма еще
             {$user->getConfirmToken()->timeTillExpiration($date)->format('%H ч. %I мин.')}");

            return false;
        }
        else
            return true;
    }
}
