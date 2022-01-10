<?php
declare(strict_types=1);
namespace App\Security\Voter;


use App\Model\User\Entity\Profile\Role\Permission;
use App\ReadModel\Profile\DetailView;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ProfileAccess extends Voter
{
    public const PROFILE_INDEX = 'profile_index';
    public const PROFILE_RECALL = 'profile_recall';
    public const PROFILE_CREATE = 'profile_create';
    public const PROFILE_ACCREDITATION = 'profile_accreditation';
    public const PROFILE_UPLOAD_FILE = 'profile_upload_file';
    public const PROFILE_DELETE_FILE = 'profile_delete_file';
    public const PAYMENT_INDEX = 'payment_index';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [
            self::PROFILE_INDEX,
            self::PROFILE_RECALL,
            self::PROFILE_CREATE,
            self::PROFILE_ACCREDITATION,
            self::PROFILE_UPLOAD_FILE,
            self::PROFILE_DELETE_FILE,
            self::PAYMENT_INDEX
        ], true) && $subject instanceof DetailView;
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_MODERATOR')){
            return true;
        }
        $user = $token->getUser();
        if (!$user instanceof UserIdentity) {
            return false;
        }

        if (!$subject instanceof DetailView) {
            return false;
        }

        if ($user->getId() !== $subject->user_id){
            return false;
        }



        switch ($attribute) {
            case self::PROFILE_INDEX:
                return $subject->isGranted(Permission::PROFILE_INDEX);
                break;
            case self::PROFILE_UPLOAD_FILE:
                //TODO РАСКОМЕНТИРОВАТЬ КОД! ПРИ ТЕСТЕ БЫЛА ЗАКОМЕЧЕНА
              /*  if (!$subject->getStatus()->isWait()){
                    return false;
                }*/
                return $subject->isGranted(Permission::PROFILE_UPLOAD_FILE);
                break;
            case self::PAYMENT_INDEX:
                if (!$subject->getStatus()->isActive()){
                    return false;
                }
                return $subject->isGranted(Permission::PAYMENT_INDEX);
                break;
            case self::PROFILE_DELETE_FILE:
                if (!$subject->getStatus()->isWait() && !$subject->getStatus()->isRejected()){
                    return false;
                }
                return $subject->isGranted(Permission::PROFILE_DELETE_FILE);
            case self::PROFILE_RECALL:
                if (!$subject->getStatus()->isModerate()){
                    return false;
                }
                return $subject->isGranted(Permission::PROFILE_ACCREDITATION_RECALL);
                break;
            case self::PROFILE_ACCREDITATION:
                return $subject->isGranted(Permission::PROFILE_ACCREDITATION_STATEMENT);
                break;
        }
        return false;
    }
}