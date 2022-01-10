<?php
declare(strict_types=1);
namespace App\Security\Voter;


use App\Model\User\Entity\Profile\Role\Permission;
use App\Model\User\Entity\Profile\Role\Role;
use App\Model\Work\Procedure\Entity\Lot\Status;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class LotAccess extends Voter
{
    public const INDEX = 'index';
    private const SIGN_FILE = 'sign_file';
    private const CREATE = 'create';
    private const SHOW = 'show';
    private const UPLOAD_FILE = 'upload_file';
    private const DELETE_FILE = 'delete_file';
    private const SIGN_XML = 'sign_xml';
    private const RECALL = 'recall';
    private const HISTORY_INDEX = 'history_index';
    private const HISTORY_SHOW = 'history_show';
    private const BIDS_FOR_LOT = "bids_for_lot";

    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }


    protected function supports(string $attribute, $subject)
    {

        return in_array($attribute, [
            self::INDEX,
            self::SIGN_FILE,
            self::CREATE,
            self::SHOW,
            self::UPLOAD_FILE,
            self::DELETE_FILE,
            self::SIGN_XML,
            self::RECALL,
            self::HISTORY_INDEX,
            self::HISTORY_SHOW,
            self::BIDS_FOR_LOT
        ], true);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::INDEX:
                return $user->getRoles();
                break;
            case self::SIGN_FILE:
                return
                    $this->security->isGranted('ROLE_WORK_MANAGE_PROJECTS') ||
                    $subject->getProject()->isMemberGranted(new Id($user->getId()), Permission::MANAGE_TASKS);
                break;

            case self::BIDS_FOR_LOT:
               return true;
                break;

            case self::CREATE:
                return
                    $this->security->isGranted('ROLE_WORK_MANAGE_PROJECTS');
                break;
        }

        return false;

    }
}