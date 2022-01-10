<?php
declare(strict_types=1);

namespace App\Widget;

use App\Model\User\Entity\User\Id;
use App\ReadModel\User\Notification\NotificationFetcher;
use App\Security\UserIdentity;
use App\Services\Main\GlobalRoleAccessor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationWidget extends AbstractExtension
{
    /**
     * @var NotificationFetcher
     */
    private $notificationFetcher;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var GlobalRoleAccessor
     */
    private $globalRoleAccessor;

    public function __construct(NotificationFetcher $notificationFetcher,
                                TokenStorageInterface $tokenStorage,
                                GlobalRoleAccessor $globalRoleAccessor
    )
    {
        $this->notificationFetcher = $notificationFetcher;
        $this->tokenStorage = $tokenStorage;
        $this->globalRoleAccessor = $globalRoleAccessor;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('notification', [$this, 'notification'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function notification(Environment $twig)
    {

        if (null === $token = $this->tokenStorage->getToken()) {
            return '';
        }

        if (!($userIdentity = $token->getUser()) instanceof UserIdentity) {
            return '';
        }

        $countUnreadNotification = $this->notificationFetcher->countUnreadNotificationUser(new Id($userIdentity->getId()));
// ---


        return $twig->render('widget/notification.html.twig',
            ['count_unread_notification' => $countUnreadNotification]
        );
    }


}