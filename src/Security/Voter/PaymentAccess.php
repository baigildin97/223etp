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
 * Class PaymentAccess
 * @package App\Security\Voter
 */
class PaymentAccess extends Voter
{

    public const PAYMENT_SHOW = 'payment_show';

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
    public function __construct(Security $security, GlobalRoleAccessor $globalRoleAccessor){
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
                self::PAYMENT_SHOW
            ], true);
    }

    /**
     * @param string $attribute
     * @param mixed $profile_id
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $profile_id, TokenInterface $token)
    {

        $user = $token->getUser();


        if (!$user instanceof UserInterface) {
            return false;
        }



        switch ($attribute) {
            case self::PAYMENT_SHOW:
                if ($this->globalRoleAccessor->isUser()) {
                    if ($user->getProfileId() === $profile_id){
                        return true;
                    }
                }else if ($this->globalRoleAccessor->isAdmin() or $this->globalRoleAccessor->isModerator()){
                    return true;
                }
                break;

            default:

        }





        return $this->security->isGranted(Permission::SHOW_AUCTION);


    }
}