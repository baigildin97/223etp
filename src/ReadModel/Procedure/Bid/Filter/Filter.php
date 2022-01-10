<?php
declare(strict_types=1);

namespace App\ReadModel\Procedure\Bid\Filter;

use App\Model\Work\Procedure\Entity\Lot\Bid\Status;

/**
 * Class Filter
 * @package App\ReadModel\Procedure\Bid\Filter
 */
class Filter
{
    /**
     * @var string|null
     */
    public $participantId;

    /**
     * @var string|null
     */
    public $lotId;

    /**
     * @var array
     */
    public $statuses;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string|null
     */
    public $lotNumber;

    /**
     * @return Filter
     */
    public static function all()
    {
        return new self(null, null, null);
    }

    /**
     * Filter constructor.
     * @param string|null $participantId
     * @param string|null $lotId
     * @param array|null $statuses
     */
    public function __construct(?string $participantId, ?string $lotId, ?array $statuses)
    {
        $this->participantId = $participantId;
        $this->lotId = $lotId;
        $this->statuses = $statuses;
    }

    /**
     * @param string $lotId
     * @return static
     */
    public static function forLot(string $lotId): self
    {
        return new self(null, $lotId, null);
    }

    /**
     * @param string $profileId
     * @return static
     */
    public static function forUserProfile(string $profileId): self
    {
        return new self($profileId, null, null);
    }

    /**
     * @param string $profileId
     * @param array $statuses
     * @return static
     */
    public static function forUserProfileStatusDraft(string $profileId, array $statuses): self
    {
        return new self($profileId, null, $statuses);
    }

    /**
     * @param string $lotId
     * @param string $participant
     * @return static
     */
    public static function participantForLot(string $lotId, string $participant): self
    {
        return new self($participant, $lotId, null);
    }

    /**
     * @param string $lotId
     * @return static
     */
    public static function organizerForLot(string $lotId): self
    {
        return new self(null, $lotId,
            [   Status::sent()->getName(),
                Status::reject()->getName(),
                Status::approved()->getName()
            ]);
    }

    /**
     * @param string $lotId
     * @param array $statuses
     * @return static
     */
    public static function bidInStatusesForLot(string $lotId, array $statuses): self
    {
        return new self(null, $lotId, $statuses);
    }

}