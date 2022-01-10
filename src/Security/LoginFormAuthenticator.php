<?php

namespace App\Security;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHashGenerator;
use App\Model\User\Service\TokenGenerator;
use App\Model\User\UseCase\User\SignUp\Request\Message;
use App\Services\Tasks\Notification;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use ReCaptcha\ReCaptcha;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordHashGenerator;
    private $emailTokenGenerator;
    private $userRepository;
    private $flusher;
    private $notificationService;
    private $router;
    private $container;
    private $reCaptcha;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        PasswordHashGenerator $passwordHashGenerator,
        TokenGenerator $emailTokenGenerator,
        UserRepository $userRepository,
        Flusher $flusher,
        Notification $notificationService,
        RouterInterface $router,
        ContainerInterface $container,
        ReCaptcha $reCaptcha
    ){
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordHashGenerator = $passwordHashGenerator;
        $this->emailTokenGenerator = $emailTokenGenerator;
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->notificationService = $notificationService;
        $this->router = $router;
        $this->container = $container;
        $this->reCaptcha = $reCaptcha;
    }

    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $request->getClientIp();
        $googleData = $this->reCaptcha->verify(
            $request->request->get('token'),
            $request->getClientIp()
        );

        //if (!$googleData->isSuccess() || $googleData->getScore() < 0.7){
        //    throw new CustomUserMessageAuthenticationException('Вы не прошли проверку ReCaptcha.');
        //}

        $credentials = [
            'email' => mb_strtolower($request->request->get('email')),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
            'ip' => $request->getClientIp()
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            mb_strtolower($credentials['email'])
        );


        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        // Load / create our user however you need.
        // You can do this by calling the user provider, or with custom logic here.
        $user = $userProvider->loadUserByUsername(mb_strtolower($credentials['email']) . '::' . $credentials['ip']);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordHashGenerator->validate($credentials['password'],$user->getPassword());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }
}
