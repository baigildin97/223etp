<?php
declare(strict_types=1);

namespace App\Security\Voter;


use App\Model\User\Entity\Profile\Role\Permission;
use App\Model\User\Entity\User\Role;
use App\ReadModel\Auction\Offers\OffersFetcher;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Profile\DetailView;
use App\Security\UserIdentity;
use App\Services\Main\GlobalRoleAccessor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AuctionAccess extends Voter
{
    public const SHOW_AUCTION = 'show_auction';
    public const SHOW_AUCTION_OFFERS = 'show_auction_offers';


    private $security;
    private $globalRoleAccessor;
    private $bidFetcher;
    private $offersFetcher;

    public function __construct(
        Security $security,
        GlobalRoleAccessor $globalRoleAccessor,
        BidFetcher $bidFetcher,
        OffersFetcher $offersFetcher
    )
    {
        $this->security = $security;
        $this->globalRoleAccessor = $globalRoleAccessor;
        $this->bidFetcher = $bidFetcher;
        $this->offersFetcher = $offersFetcher;
    }

    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [
                self::SHOW_AUCTION
            ], true) && $subject;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserIdentity) {
            return false;
        }

        if ($subject['auction']->isCompleted()){
            return false;
        }

        switch ($attribute) {
            case self::SHOW_AUCTION:


                if ($this->globalRoleAccessor->isUser()) {

                    if ($this->globalRoleAccessor->isParticipant()) {
                        if(!$this->bidFetcher->findMyBid($subject['lot_id'], $subject['profile']->id)){
                            return false;
                        }
                        return true;
                    }

                    return true;

                } else if ($this->globalRoleAccessor->isAdmin() or $this->globalRoleAccessor->isModerator()) {
                    return true;
                }

                return $this->security->isGranted(Permission::SHOW_AUCTION);
                break;

        }
        return false;
    }
}