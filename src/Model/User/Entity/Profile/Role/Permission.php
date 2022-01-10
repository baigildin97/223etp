<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Role;


use Webmozart\Assert\Assert;

class Permission
{
    public const SHOW_PROCEDURE = 'show_procedure';
    public const CREATE_PROCEDURE = 'create_procedure';
    public const EDIT_PROCEDURE = 'edit_procedures';
    public const RECALL_PROCEDURE = 'recall_procedure';
    public const SIGN_XML_PROCEDURE = 'sign_xml_procedure';
    public const UPLOAD_FILE_TO_PROCEDURE = 'upload_file_to_procedure';
    public const SIGN_FILE_TO_PROCEDURE = 'sign_file_to_procedure';
    public const DELETE_FILE_TO_PROCEDURE = 'delete_file_to_procedure';
    public const CREATE_PROTOCOL_PROCEDURE = 'create_protocol_procedure';
    public const CREATE_NOTIFICATIONS = 'create_notifications';
    public const RECALL_NOTIFICATION = 'recall_notifications';
    public const PUBLICATION_NOTIFICATION = 'publication_notifications';
    public const CREATE_BID = 'create_bid';
    public const SHOW_BID = 'show_bid';
    public const EDIT_BID = 'edit_bid';
    public const RECALL_BID = 'recall_bid';
    public const SIGN_XML_BID = 'sign_xml_bid';
    public const UPLOAD_FILE_TO_BID = 'upload_file_to_xml_bid';
    public const SIGN_FILE_TO_BID = 'sign_file_to_xml_bid';
    public const DELETE_FILE_TO_BID = 'delete_file_to_xml_bid';
    public const SHOW_LOT = 'show_lot';
    public const CREATE_LOT = 'create_lot';
    public const EDIT_LOT = 'edit_lot';
    public const UPLOAD_FILE_TO_LOT = 'upload_file_to_lot';
    public const SIGN_FILE_TO_LOT = 'sign_file_to_lot';
    public const DELETE_FILE_TO_LOT = 'delete_file_to_lot';
    public const DELETE_LOT = 'delete_lot';
    public const PROFILE_INDEX = 'profile_index';
    public const PROFILE_ACCREDITATION_STATEMENT = 'accreditation_statement';
    public const PROFILE_ACCREDITATION_RECALL = 'accreditation_recall';
    public const PROFILE_UPLOAD_FILE = 'profile_upload_file';
    public const PROFILE_DELETE_FILE = 'profile_delete_file';
    public const PAYMENT_INDEX = 'payment_index';
    public const PAYMENT_SHOW = 'payment_show';
    public const BIDS_FOR_LOT = 'bids_for_lot';
    public const SHOW_AUCTION = 'show_auction';
    public const SHOW_AUCTION_OFFERS = 'show_auction_offers';
    public const BET_OFFER_AUCTION = 'bet_offer_auction';
    public const CERTIFICATE_SHOW = 'certificate_show';
    public const REVIEW_BIDS = 'review_bids';
    public const NOTIFICATION_SHOW = 'notification_show';


    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, self::names());
        $this->name = $name;
    }

    public static function names(): array
    {
        return [
            self::SHOW_PROCEDURE,
            self::CREATE_PROCEDURE,
            self::EDIT_PROCEDURE,
            self::RECALL_PROCEDURE,
            self::SIGN_XML_PROCEDURE,
            self::UPLOAD_FILE_TO_PROCEDURE,
            self::SIGN_FILE_TO_PROCEDURE,
            self::RECALL_NOTIFICATION,
            self::DELETE_FILE_TO_PROCEDURE,
            self::PUBLICATION_NOTIFICATION,
            self::CREATE_PROTOCOL_PROCEDURE,
            self::CREATE_NOTIFICATIONS,
            self::CREATE_BID,
            self::SHOW_BID,
            self::EDIT_BID,
            self::RECALL_BID,
            self::SIGN_XML_BID,
            self::UPLOAD_FILE_TO_BID,
            self::SIGN_FILE_TO_BID,
            self::DELETE_FILE_TO_BID,
            self::SHOW_LOT,
            self::CREATE_LOT,
            self::EDIT_LOT,
            self::UPLOAD_FILE_TO_LOT,
            self::SIGN_FILE_TO_LOT,
            self::DELETE_FILE_TO_LOT,
            self::DELETE_LOT,
            self::PROFILE_ACCREDITATION_STATEMENT,
            self::PROFILE_ACCREDITATION_RECALL,
            self::PROFILE_INDEX,
            self::PROFILE_UPLOAD_FILE,
            self::PROFILE_DELETE_FILE,
            self::PAYMENT_INDEX,
            self::PAYMENT_SHOW,
            self::BIDS_FOR_LOT,
            self::SHOW_AUCTION,
            self::SHOW_AUCTION_OFFERS,
            self::BET_OFFER_AUCTION,
            self::CERTIFICATE_SHOW,
            self::REVIEW_BIDS,
            self::NOTIFICATION_SHOW
        ];
    }

    public function isNameEqual(string $name): bool
    {
        return $this->name === $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}