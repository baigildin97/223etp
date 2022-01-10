<?php


namespace App\Security;


use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHashGenerator;
use App\ReadModel\Certificate\CertificateFetcher;
use App\Services\CryptoPro\CryptoPro;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginCryptAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordHashGenerator;
    private $certificateFetcher;

    public function __construct(UrlGeneratorInterface $urlGenerator,
                                CsrfTokenManagerInterface $csrfTokenManager,
                                PasswordHashGenerator $passwordHashGenerator,
                                CertificateFetcher $certificateFetcher)
    {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordHashGenerator = $passwordHashGenerator;
        $this->certificateFetcher = $certificateFetcher;
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('app_login_crypt');
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return 'app_login_crypt' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request): array
    {
        try {
            $certData = CryptoPro::getCertInfo(
                $request->get('form_data'),
                $request->get('form_signedData')
            );
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException('Не удалось проверить сертификат. '.$e->getMessage());
        }

        $credentials = [
            'csrf_token' => $request->get('_csrf_token'),
            'email' => $certData['email'],
            'thumbprint' => $certData['thumbprint'],
            'ip' => $request->getClientIp()
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * @param $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $userProvider->loadUserByUsername(
            $this->certificateFetcher->getUserEmailByThumbprint($credentials['thumbprint']) . '::' . $credentials['ip']
        );

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    /**
     * @param $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if ($cert = $this->certificateFetcher->findDetailByThumbprint($credentials['thumbprint']))
            return $credentials['email'] === $cert->subject_name_email;
        else
            return false;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }
}
