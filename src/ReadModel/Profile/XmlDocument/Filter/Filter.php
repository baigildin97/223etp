<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\XmlDocument\Filter;

use App\Model\User\Entity\Profile\XmlDocument\StatusTactic;
use App\Model\User\Entity\Profile\XmlDocument\TypeStatement;

/**
 * Class Filter
 * @package App\ReadModel\Profile\XmlDocument\Filter
 */
class Filter
{
    /**
     * @var string|null
     */
    public $profileId;

    /**
     * @var string | null
     */
    public $statusTactic;

    /**
     * @var string | null
     */
    public $userName;

    /**
     * @var string | null
     */
    public $subjectNameInn;

    /**
     * @var string | null
     */
    public $email;

    /**
     * @var string | null
     */
    public $phone;

    /**
     * @var string | null
     */
    public $clientIp;

    /**
     * @var string|null
     */
    public $moderatorId;

    /**
     * @var array | null
     */
    public $type;

    /**
     * Filter constructor.
     * @param string|null $profileId
     * @param string $statusTactic
     * @param string|null $moderatorId
     * @param string|null $type
     */
    public function __construct(? string $profileId, ? string $statusTactic, ? string $moderatorId, ? array $type) {
        $this->profileId = $profileId;
        $this->statusTactic = $statusTactic;
        $this->moderatorId = $moderatorId;
        $this->type = $type;
    }

    /**
     * @param string $profileId
     * @return static
     */
    public static function fromProfile(string $profileId): self {
        return new self($profileId, null, null, null);
    }

    /**
     * @param string $status
     * @return static
     */
    public static function fromStatusTactic(string $statusTactic): self {
        return new self(null, $statusTactic, null, null);
    }

    /**
     * @param string $moderatorId
     * @param string $statusTactic
     * @return static
     */
    public static function fromModeratorProcessing(string $moderatorId, string $statusTactic): self {
        return new self(null, $statusTactic,$moderatorId, null);
    }

    /**
     * @return static
     */
    public static function fromModerateList(): self {
        return new self(null, StatusTactic::published()->getName(), null, [
            TypeStatement::edit()->getName(),
            TypeStatement::registration()->getName(),
            TypeStatement::replacingEp()->getName()
        ]);
    }
}