<?php
declare(strict_types=1);
namespace App\Security;


use App\Model\User\Entity\User\Status;
use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $fetcher;

    public function __construct(UserFetcher $fetcher) {
        $this->fetcher = $fetcher;
    }

    public function loadUserByUsername($username): UserInterface {
        $userData = explode('::', $username);

        $user = $this->fetcher->findForAuth(mb_strtolower($userData[0]));

        if (!$user){
            throw new UsernameNotFoundException('');
        }

        return self::identityByUser($user, $userData[1]);
    }

    public function refreshUser(UserInterface $identity): UserInterface {
        if (!$identity instanceof UserIdentity){
            throw new UnsupportedUserException('Invalid user class'.get_class($identity));
        }

        $user = $this->loadUser($identity->getEmail());

        if ($user->status === Status::blocked()->getName())
            throw new CustomUserMessageAuthenticationException('User is blocked');

        return self::identityByUser($user, $identity->getIp());
    }

    private function loadUser($email): AuthView {
        if (!$user = $this->fetcher->findForAuth($email)){
            throw new UsernameNotFoundException('');
        }

        return $user;
    }

    private static function identityByUser(AuthView $user, ?string $ip): UserIdentity {
        if ($ip === null)
            throw new CustomUserMessageAuthenticationException('Authentication error: could not get client IP address');

        return new UserIdentity(
            $user->id,
            $user->email,
            $user->password_hash,
            $user->role,
            $user->status,
            $ip,
            $user->profile_id
        );
    }

    public function supportsClass($class){
        return $class === UserIdentity::class;
    }
}