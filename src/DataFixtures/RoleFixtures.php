<?php
namespace App\DataFixtures;

use App\Model\User\Entity\Profile\Role\Id;
use App\Model\User\Entity\Profile\Role\Permission;
use App\Model\User\Entity\Profile\Role\Role;
use App\Model\User\Entity\Profile\Role\RoleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture implements FixtureGroupInterface
{
    private $roleRepository;

     public static function getGroups(): array{
         return ['roles'];
     }

     public function __construct(RoleRepository $roleRepository){
        $this->roleRepository = $roleRepository;
     }

    /**
     * Добавление новых ролей участника и организатора
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager){

        $role = new Role(
            Id::next(),
            'Организатор',
            Role::ROLE_ORGANIZER,
            $this->findPermissionOrganizer()->toArray()
        );
        $this->roleRepository->add($role);


        $role = new Role(
         Id::next(),
         'Участник',
         Role::ROLE_PARTICIPANT,
         $this->findPermissionParticipant()
        );
        $this->roleRepository->add($role);
        $manager->flush();

    }

    /**
     * Список разрешений для участника
     * @return array
     */
    private function findPermissionParticipant(): array {
        $permissionsParticipant = [];
        $permissionsParticipant[] = Permission::SHOW_BID;
        $permissionsParticipant[] = Permission::EDIT_BID;
        $permissionsParticipant[] = Permission::RECALL_BID;
        $permissionsParticipant[] = Permission::SIGN_XML_BID;
        $permissionsParticipant[] = Permission::UPLOAD_FILE_TO_BID;
        $permissionsParticipant[] = Permission::SIGN_FILE_TO_BID;
        $permissionsParticipant[] = Permission::DELETE_FILE_TO_BID;
        $permissionsParticipant[] = Permission::PROFILE_ACCREDITATION_STATEMENT;
        $permissionsParticipant[] = Permission::PROFILE_ACCREDITATION_RECALL;
        $permissionsParticipant[] = Permission::PROFILE_INDEX;
        $permissionsParticipant[] = Permission::PROFILE_UPLOAD_FILE;
        $permissionsParticipant[] = Permission::NOTIFICATION_SHOW;
        $permissionsParticipant[] = Permission::PROFILE_DELETE_FILE;
        $permissionsParticipant[] = Permission::PAYMENT_INDEX;
        $permissionsParticipant[] = Permission::PAYMENT_SHOW;
        $permissionsParticipant[] = Permission::SHOW_PROCEDURE;
        $permissionsParticipant[] = Permission::SHOW_AUCTION;
        $permissionsParticipant[] = Permission::CERTIFICATE_SHOW;
        $permissionsParticipant[] = Permission::BET_OFFER_AUCTION;
        $permissionsParticipant[] = Permission::CREATE_BID;
        $permissionsParticipant[] = Permission::SHOW_LOT;
        $permissionsParticipant[] = Permission::SHOW_AUCTION_OFFERS;

        return $permissionsParticipant;
    }

    /**
     * Список разрешений для организтора
     * @return ArrayCollection
     */
    private function findPermissionOrganizer(): ArrayCollection {
        return new ArrayCollection([
            Permission::SHOW_PROCEDURE,
            Permission::CREATE_PROCEDURE,
            Permission::RECALL_PROCEDURE,
            Permission::SIGN_XML_PROCEDURE,
            Permission::UPLOAD_FILE_TO_PROCEDURE,
            Permission::SIGN_FILE_TO_PROCEDURE,
            Permission::DELETE_FILE_TO_PROCEDURE,
            Permission::SHOW_BID,
            Permission::SHOW_LOT,
            Permission::CREATE_LOT,
            Permission::EDIT_LOT,
            Permission::DELETE_LOT,
            Permission::PROFILE_ACCREDITATION_STATEMENT,
            Permission::PROFILE_ACCREDITATION_RECALL,
            Permission::PROFILE_INDEX,
            Permission::PROFILE_UPLOAD_FILE,
            Permission::NOTIFICATION_SHOW,
            Permission::PROFILE_DELETE_FILE,
            Permission::REVIEW_BIDS,
            Permission::PAYMENT_INDEX,
            Permission::BIDS_FOR_LOT,
            Permission::CREATE_PROTOCOL_PROCEDURE,
            Permission::CREATE_NOTIFICATIONS,
            Permission::UPLOAD_FILE_TO_LOT,
            Permission::RECALL_NOTIFICATION,
            Permission::PUBLICATION_NOTIFICATION,
            Permission::SHOW_AUCTION,
            Permission::CERTIFICATE_SHOW,
            Permission::SHOW_AUCTION_OFFERS,
            Permission::SIGN_FILE_TO_LOT,
            Permission::DELETE_FILE_TO_LOT,
            Permission::PAYMENT_SHOW,
            Permission::EDIT_PROCEDURE,
            Permission::DELETE_FILE_TO_PROCEDURE
        ]);


    }


}