<?php
declare(strict_types=1);
namespace App\Security\Voter;


use App\Model\User\Entity\Profile\Role\Permission;
use App\Services\Main\GlobalRoleAccessor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CertificateAccess extends Voter
{
    public const CERTIFICATE_SHOW = 'certificate_show';


    private $security;

    private $globalRoleAccessor;

    public function __construct(Security $security, GlobalRoleAccessor $globalRoleAccessor){
        $this->security = $security;
        $this->globalRoleAccessor = $globalRoleAccessor;
    }

    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [
            self::CERTIFICATE_SHOW
        ], true);
    }

    protected function voteOnAttribute(string $attribute, $user_id, TokenInterface $token)
    {

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CERTIFICATE_SHOW:
                if ($this->globalRoleAccessor->isUser()) {
                    if ($user->getId() === $user_id){
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