<?php
declare(strict_types=1);

namespace App\Security\Voter;


use App\Model\User\Entity\Profile\Role\Permission;
use App\ReadModel\Profile\Payment\DetailView;
use App\Security\UserIdentity;
use App\Services\Main\GlobalRoleAccessor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class NotificationAccess
 * @package App\Security\Voter
 */
class NotificationAccess extends Voter
{

    public const NOTIFICATION_SHOW = 'notification_show';

    /**
     * @var Security
     */
    private $security;

    /**
     * @var GlobalRoleAccessor
     */
    private $globalRoleAccessor;

    /**
     * PaymentAccess constructor.
     * @param Security $security
     * @param GlobalRoleAccessor $globalRoleAccessor
     */
    public function __construct(Security $security, GlobalRoleAccessor $globalRoleAccessor)
    {
        $this->security = $security;
        $this->globalRoleAccessor = $globalRoleAccessor;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [
            self::NOTIFICATION_SHOW
        ], true);
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }


        // Администрация может смотреть любые извещения
        // Организатор может видеть все свои извещения
        // Участники могут видеть только опубликованные извещения
        switch ($attribute) {
            case self::NOTIFICATION_SHOW:
                if ($this->globalRoleAccessor->isAdmin() or $this->globalRoleAccessor->isModerator()) {
                    return true;
                }
                if ($this->globalRoleAccessor->isOrganizer()){
                    if ($this->globalRoleAccessor->getIdProfile() === $subject->id){
                        return true;
                    }

                    if ($subject->isGranted(Permission::NOTIFICATION_SHOW)) {
                        if ($user->getId() === $subject->user_id) {
                            return true;
                        }
                    }
                }

                //TODO проверить
               /* if ($this->globalRoleAccessor->isUser()){
                    if (!$subject->getStatus()->isNew()){
                        return true;
                    }
                }*/
                return true;
                break;
            default:
        }
        return false;
    }
}