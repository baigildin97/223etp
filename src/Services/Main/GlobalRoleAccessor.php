<?php
declare(strict_types=1);

namespace App\Services\Main;


use App\Model\User\Entity\Profile\Role\Permission;
use App\Model\User\Entity\Profile\Role\Role;
use App\Model\User\Entity\Profile\Status;
use App\Model\User\Entity\Profile\XmlDocument\TypeStatement;
use App\Model\Work\Procedure\Entity\Protocol\Type;
use App\ReadModel\Auction\AuctionFetcher;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\ReadModel\Profile\XmlDocument\DetailView;
use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use App\Security\UserIdentity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GlobalRoleAccessor
 * @package App\Services\Main
 */
class GlobalRoleAccessor
{
    private $profile;

    private $userIdentity;

    private $bidFetcher;

    private $lotFetcher;

    private $procedureFetcher;

    private $auctionFetcher;

    private $profileFetcher;

    private $profileXmlDocumentFetcher;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ProfileFetcher $profileFetcher,
        BidFetcher $bidFetcher,
        LotFetcher $lotFetcher,
        ProcedureFetcher $procedureFetcher,
        AuctionFetcher $auctionFetcher,
        XmlDocumentFetcher $xmlDocumentFetcher
    )
    {
        $this->bidFetcher = $bidFetcher;
        $this->lotFetcher = $lotFetcher;
        $this->procedureFetcher = $procedureFetcher;
        $this->auctionFetcher = $auctionFetcher;
        $this->profileXmlDocumentFetcher = $xmlDocumentFetcher;
        $this->profileFetcher = $profileFetcher;

        if (null === $token = $tokenStorage->getToken()) {
            return '';
        }

        if (!($userIdentity = $token->getUser()) instanceof UserIdentity) {
            return '';
        }

        $profile = $profileFetcher->findDetailByUserId($userIdentity->getId());
        $this->userIdentity = $userIdentity;
        $this->profile = $profile;
    }

    public function getIdUser(): ?string
    {
        return $this->userIdentity->getId();
    }

    public function getIdProfile(): ?string
    {
        if ($this->issetProfile()) {
            return $this->profile->id;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isLegalEntity(): bool
    {
        if ($this->profile === null) {
            return false;
        }
        return $this->profile->isLegalEntity();
    }

    /**
     * @return bool
     */
    public function isIndividualEntrepreneur(): bool
    {
        if ($this->profile === null) {
            return false;
        }
        return $this->profile->isIndividualEntrepreneur();
    }

    /**
     * @return bool
     */
    public function isIndividual(): bool
    {
        if ($this->profile === null) {
            return false;
        }
        return $this->profile->isIndividual();
    }

    /**
     * @return bool
     */
    public function isIndividualOrIndividualEntrepreneur(): bool
    {
        if ($this->profile === null) {
            return false;
        }
        return $this->profile->isIndividualOrIndividualEntrepreneur();
    }

    /**
     * @return bool
     */
    public function isAccreditation(): bool
    {
        if ($this->issetProfile()) {
            return $this->status()->isActive();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isOrganizer(): bool
    {
        if ($this->issetProfile()) {
            return $this->profile->role_constant === Role::ROLE_ORGANIZER;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isParticipant(): bool
    {
        if ($this->issetProfile()) {
            return $this->profile->role_constant === Role::ROLE_PARTICIPANT;
        }
        return false;
    }


    public function isOrganizerOrIsParticipant(): bool
    {
        if ($this->issetProfile()) {
            return $this->profile->isOrganizerOrIsParticipant();
        }
        return false;
    }

    public function isNotParticipant(): bool
    {
        if ($this->issetProfile()) {
            return $this->profile->role_constant !== Role::ROLE_PARTICIPANT;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->getRole() === 'ROLE_MODERATOR';
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->getRole() === 'ROLE_ADMIN';
    }

    /**
     * @return bool
     */
    public function isModeratorOrAdmin(): bool
    {
        if ($this->isModerator() or $this->isAdmin()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->getRole() === 'ROLE_USER';
    }

    /**
     * @return bool
     */
    public function isAccreditedUser(): bool
    {
        if ($this->issetProfile()) {
            return $this->status()->isActive() && $this->isUser();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function issetProfile(): bool
    {
        return isset($this->profile) ?? false;
    }

    /**
     * @param string $lotId
     * @return bool
     */
    public function canCreateBidToLot(string $lotId): bool
    {
        $existsLotByStatus = $this->lotFetcher->existsLotByStatus($lotId, \App\Model\Work\Procedure\Entity\Lot\Status::acceptingApplications());
        if ($this->isParticipant() && $existsLotByStatus && $this->isAccreditation()) {
            if (!$this->bidFetcher->existsActiveBidForLot($this->profile->id, $lotId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $procedureId
     * @return bool
     */
    public function canCreateLot(string $procedureId): bool
    {
        if ($this->issetProfile()) {
            $procedure = $this->procedureFetcher->findDetail($procedureId);

            if (!$procedure->getStatus()->isNew()) {
                return false;
            }

            if ($this->profile->id !== $procedure->profile_id) {
                return false;
            }
            return $this->profile->isGranted(Permission::CREATE_LOT);
        }
        return false;
    }

    /**
     * @param string $procedureId
     * @return bool
     */
    public function canCreateProcedureNotifications(string $procedureId): bool
    {
        if ($this->isOrganizer()) {
            $procedure = $this->procedureFetcher->findDetail($procedureId);

//            if (!$procedure->getStatus()->isNew() && !$procedure->getStatus()->isRejected()) {
//                return false;
//            }

            if ($this->profile->id !== $procedure->profile_id) {
                return false;
            }

            return $this->profile->isGranted(Permission::CREATE_NOTIFICATIONS);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canEditLot(string $lotId): bool
    {
        if ($this->isOrganizer()) {
            $lot = $this->lotFetcher->findDetail($lotId);

            if (!$lot->getStatus()->isNew()) {
                return false;
            }

            if ($this->profile->id !== $lot->organizer_profile_id) {
                return false;
            }

            return $this->profile->isGranted(Permission::EDIT_LOT);
        }
        return false;
    }

    /**
     * @param string $procedureId
     * @return bool
     */
    public function canEditProcedure(string $procedureId): bool
    {
        if ($this->isOrganizer()) {
            $procedure = $this->procedureFetcher->findDetail($procedureId);

            if (!$procedure->getStatus()->isNew() and !$procedure->getStatus()->isRejected()){
                return false;
            }

            if ($this->profile->id !== $procedure->profile_id) {
                return false;
            }

            return $this->profile->isGranted(Permission::EDIT_PROCEDURE);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canUploadFileLot(): bool
    {
        if ($this->issetProfile()) {
            return $this->profile->isGranted(Permission::UPLOAD_FILE_TO_LOT);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canDeleteFileLot(): bool
    {
        if ($this->issetProfile()) {
            return $this->profile->isGranted(Permission::DELETE_FILE_TO_LOT);
        }
        return false;
    }


    /**
     * @param string $lotId
     * @return bool
     */
    public function canReviewBids(string $lotId): bool
    {
        if ($this->isAdmin() or $this->isModerator()) {
            return true;
        }

        if (!$this->isOrganizer())
            return false;

        $procedure = $this->lotFetcher->findDetail($lotId);

        if ($procedure->getStatus()->isRejected()) {
            return false;
        }

        if ($procedure->getStatus()->isModerate()) {
            return false;
        }

        if ($procedure->getStatus()->isModerated()) {
            return false;
        }

        if ($procedure->getStatus()->isAcceptingApplications()) {
            return false;
        }

        if ($procedure->getStatus()->isNew()) {
            return false;
        }
        if ($this->isOrganizer()) {
            return $this->profile->isGranted(Permission::REVIEW_BIDS);
        }


        if ($this->profile->id !== $procedure->organizer_profile_id) {
            return false;
        }

        if ($procedure->getStatus()->isAcceptingApplications()) {
            return false;
        }


        return false;
    }

    /**
     * @param string $bidId
     * @return bool
     * @throws \Exception
     */
    public function canReviewBid(string $bidId): bool
    {
        if ($this->isOrganizer()) {
            $bid = $this->bidFetcher->findDetail($bidId);
//            if (!(new \App\Model\Work\Procedure\Entity\Lot\Status($bid->lot_status))->isApplicationsReceived()) {
//                return false;
//            }

//            if (new \DateTimeImmutable() < new \DateTimeImmutable($bid->closing_date_of_applications)) {
//                return false;
//            }
//
//            if (!new \DateTimeImmutable() > new \DateTimeImmutable($bid->summing_up_applications)) {
//                return false;
//            }

            if ($bid->organizer_profile_id !== $this->profile->id) {
                return false;
            }

            if (!$bid->getStatus()->isSent()) {
                return false;
            }

            return $this->profile->isGranted(Permission::REVIEW_BIDS);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canSignFileLot(): bool
    {
        if ($this->issetProfile()) {
            return $this->profile->isGranted(Permission::SIGN_FILE_TO_LOT);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canCreateProtocolProcedure(): bool
    {
        if ($this->issetProfile()) {
            return $this->profile->isGranted(Permission::CREATE_PROTOCOL_PROCEDURE);
        }
        return false;
    }

//    /**
//     * @return bool
//     */
//    public function canCreateNotifications(): bool{
//        if($this->issetProfile()){
//            return $this->profile->isGranted(Permission::CREATE_NOTIFICATIONS);
//        }
//        return false;
//    }

    /**
     * @param string $procedureId
     * @return bool
     */
    public function canProcedureNotificationRecall(string $procedureId): bool
    {
        $procedure = $this->procedureFetcher->findDetail($procedureId);

        if (!$this->isUser()) {
            return false;
        }

        if ($this->profile->id !== $procedure->profile_id) {
            return false;
        }

        if (!$procedure->getStatus()->isModerate()) {
            return false;
        }

        return true;
    }

    /**
     * @param string $bidId
     * @return bool
     */
    public function canBidRecall(string $bidId): bool
    {
        if ($this->issetProfile()) {
            $bid = $this->bidFetcher->findDetail($bidId);

            if (!$bid->getStatus()->isSent()) {
                return false;
            }

            if ($this->profile->id !== $bid->participant_id) {
                return false;
            }

            return $this->profile->isGranted(Permission::RECALL_BID);
        }
        return false;
    }

    /**
     * @param string $procedureId
     * @return bool
     */
    public function canPublicationNotification(string $procedureId): bool
    {
        if ($this->issetProfile()) {
            $procedure = $this->procedureFetcher->findDetail($procedureId);

            if (!$procedure->getStatus()->isModerated()) {
                return false;
            }

            if ($this->profile->id !== $procedure->profile_id) {
                return false;
            }

            return $this->profile->isGranted(Permission::PUBLICATION_NOTIFICATION);
        }
        return false;
    }

    /**
     * @param string $auctionId
     * @return bool
     */
    public function canShowAuction(string $auctionId): bool
    {
        $auction = $this->auctionFetcher->findDetail($auctionId);

        if ($this->isUser()) {
            if ($this->issetProfile()) {

                if (!$auction->getStatus()->isActive()) {
                    return false;
                }

                if ($this->isParticipant()) {
                    $bid = $this->bidFetcher->findApprovedBidByParticipant($auction->lot_id, $this->getIdProfile());
                    if (!$bid) return false;
                }

                return $this->profile->isGranted(Permission::SHOW_AUCTION);
            }
        }

        if ($this->isAdmin() or $this->isModerator()) {

            if (!$auction->getStatus()->isActive()) {
                return false;
            }

            return true;
        }

        return false;
    }


    /**
     * @return bool
     */
    public function canUploadFileProcedure(): bool
    {
        if ($this->profile !== null) {
            return $this->profile->isGranted(Permission::UPLOAD_FILE_TO_PROCEDURE);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canSignFileProcedure(): bool
    {
        if ($this->profile !== null) {
            return $this->profile->isGranted(Permission::SIGN_FILE_TO_PROCEDURE);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function catDeleteFileProcedure(): bool
    {
        if ($this->profile !== null) {
            return $this->profile->isGranted(Permission::DELETE_FILE_TO_PROCEDURE);
        }
        return false;
    }

    /**
     * @return string
     */
    private function getRole(): string
    {
        return (new ArrayCollection($this->userIdentity->getRoles()))->first();
    }


    /**
     * @return Status
     */
    private function status(): Status
    {
        return new Status($this->profile->status);
    }

    /**
     * @return bool
     */
    public function issetModerateProcessingXmlDocProfile(): bool
    {
        if (!$this->issetProfile()) {
            return false;
        }
        return $this->profileXmlDocumentFetcher->existsXmlDocumentByProfileModerateProcessing($this->profile->id);
    }

    /**
     * @param DetailView $xml_document_id
     * @return bool
     * Разрешение отзыва заявки на регистрацию
     */
    public function canRecallStatementRegistration(DetailView $xml_document_id): bool
    {
        if (!$this->issetProfile()) {
            return false;
        }
        if ($xml_document_id->isRecalled()) {
            return false;
        }
        if ((new TypeStatement($xml_document_id->type))->isRecall()) {
            return false;
        }
        if (!$xml_document_id->isSigned()) {
            return false;
        }
        return ($this->isOrganizer() || $this->isParticipant());
    }

    /**
     * @param \App\ReadModel\Procedure\XmlDocument\DetailView $detailView
     * @return bool
     * Отыв извещения из модерации
     */
    public function canRecallNoticeProcedure(\App\ReadModel\Procedure\XmlDocument\DetailView $detailView): bool
    {
        if (!$this->isOrganizer()) {
            return false;
        }
        if (!$detailView->isModerate()) {
            return false;
        }

        return true;
    }

    public function canShowNoticeHistoryProcedure(\App\ReadModel\Procedure\XmlDocument\DetailView $detailView): bool
    {
        if (!$this->isOrganizer()) {
            return false;
        }
        if (!$detailView->isModerate()) {
            return false;
        }

        return true;
    }

    /**
     * @param \App\ReadModel\Procedure\XmlDocument\DetailView $detailView
     * @return bool
     */
    public function canSignNotice(\App\ReadModel\Procedure\XmlDocument\DetailView $detailView): bool
    {
        if (!$this->isOrganizer()) {
            return false;
        }
        if (!$detailView->isApproved()) {
            return false;
        }

        return true;
    }

    public function canCloneProcedure(string $procedureId): bool
    {
        if ($this->isOrganizer()) {
            $procedure = $this->procedureFetcher->findDetail($procedureId);


            if ($this->profile->id !== $procedure->profile_id) {
                return false;
            }

            return true;
        }
        return false;
    }

    public function canShowProtocols(string $procedureId, string $lotId): bool
    {
        if ($this->isModeratorOrAdmin()){
            return true;
        }

        if ($this->isOrganizer()) {
            $procedure = $this->procedureFetcher->findDetail($procedureId);
            if ($procedure->getStatus()->isNew() or $procedure->getStatus()->isModerate() or $procedure->getStatus()->isRejected()) {
                return false;
            }

            if ($this->profile->id !== $procedure->profile_id) {
                return false;
            }
            return true;
        }

        if ($this->isParticipant()) {

            return true;
        }

        return false;
    }

    public function canCancellingPublication(string $procedureId): bool
    {
        if ($this->isOrganizer()) {
            $procedure = $this->procedureFetcher->findDetail($procedureId);

            if ($procedure->getStatus()->getName() !== \App\Model\Work\Procedure\Entity\Status::moderated()->getName()
                and $procedure->getStatus()->getName() !== \App\Model\Work\Procedure\Entity\Status::active()->getName()) {
                return false;
            }

            if ($this->profile->id !== $procedure->profile_id) {
                return false;
            }

            return true;

        }

        return false;
    }

    public function canShowProtocol(string $lotId, string $protocolId, string $protocolType): bool
    {
        $lot = $this->lotFetcher->findDetail($lotId);
        $procedure = $this->procedureFetcher->findDetail($lot->procedure_id);

        if ($this->isModeratorOrAdmin()) {
            return true;
        }

        if ($this->isOrganizer()) {

            if ($this->profile->id !== $procedure->profile_id) {
                return false;
            }

            return true;
        }

        if ($this->isParticipant()) {
            if ($lot->winner_id === $this->getIdProfile()) {
                if ((new Type($protocolType))->isResultProtocol()) {
                    return true;
                }
            }


            return false;
        }


        return false;

    }

    public function canConfirmResetEP(string $profileId): bool
    {
        if (!$this->isModeratorOrAdmin()) {
            return false;
        }
        $profile = $this->profileFetcher->find($profileId);
        if (!$profile->getStatus()->isReplacingEp()) {
            return false;
        }
        return true;
    }

    public function canDeleteFileProfile(string $profileId): bool
    {
        $profile = $this->profileFetcher->find($profileId);
        if ($profile->getStatus()->isBlocked()) {
            return false;
        }

        if ($profile->getStatus()->isArchived()) {
            return false;
        }

        if ($profile->getStatus()->isModerate()) {
            return false;
        }

        if ($this->isModeratorOrAdmin()) {
            return false;
        }

        return true;
    }

    public function canDownloadQuest(string $profileId)
    {
        $profile = $this->profileFetcher->find($profileId);
        if ($profile->getStatus()->isWait()) {
            return true;
        }
        if ($profile->getStatus()->isRejected()) {
            return true;
        }
        return false;
    }

    public function canShowBidParticipant(string $procedure_id): bool
    {
        $procedure = $this->procedureFetcher->findDetail($procedure_id);
        if ($this->isOrganizer()) {
            if ($procedure->getStatus()->isAcceptingApplications()) {
                return false;
            }
        }

        return true;
    }

    public function isPaymentWinnerConfirm(string $procedureId): bool
    {
        if ($this->isUser()) {
            $lot = $this->lotFetcher->findDetailByProcedureId($procedureId);

            if ($this->profile->id !== $lot->organizer_profile_id) {
                return false;
            }


            if (!$lot->getStatus()->isStatusSignedProtocolResult()) {
                return false;
            }


            if ($lot->payment_winner_confirm !== 1) {
                return true;
            }
        }

        if ($this->isModeratorOrAdmin()){
            return false;
        }

        return false;
    }

    public function canShowBidDocuments(string $bidId): bool {
        $bid = $this->bidFetcher->findDetail($bidId);
        if ($this->isParticipant()){
            if ($bid->getStatus()->isNew()){
                return true;
            }
        }

        if ($this->isAdmin()){
            return true;
        }

        if ($this->isOrganizer()){
            return true;
        }

        return false;
    }

}