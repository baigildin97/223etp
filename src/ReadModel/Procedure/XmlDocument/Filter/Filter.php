<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\XmlDocument\Filter;


use App\Model\Work\Procedure\Entity\XmlDocument\Status;
use App\Model\Work\Procedure\Entity\XmlDocument\Type;

/**
 * Class Filter
 * @package App\ReadModel\Procedure\XmlDocument\Filter
 */
class Filter
{
    /**
     * @var string|null
     */
    public $procedureId;

    /**
     * @var string|null
     */
    public $type;

    /**
     * @var string|null
     */
    public $status;

    /**
     * @var string|null
     */
    public $moderator;

    /**
     * @var string|null
     */
    public $statusTactic;

    /**
     * Filter constructor.
     * @param string|null $procedureId
     * @param string|null $type
     * @param string|null $status
     * @param string|null $moderator
     */
    public function __construct(? string $procedureId, ? string $type, ? string $status, ? string $moderator, ? string $statusTactic) {
        $this->procedureId = $procedureId;
        $this->type = $type;
        $this->status = $status;
        $this->moderator = $moderator;
        $this->statusTactic = $statusTactic;
    }

    /**
     * @param string $procedureId
     * @return static
     */
    public static function fromProcedure(string $procedureId): self {
        return new self($procedureId, null, null, null, null);
    }

    public static function fromProcedureByStatus(string $procedureId, Status $status): self{
        return new self($procedureId, null, $status->getName(),null,null);
    }

    /**
     * @param Type $type
     * @param string $procedureId
     * @return static
     */
    public static function fromTypeAndProcedure(Type $type, string $procedureId): self{
        return new self($procedureId, $type->getName(), null, null, null);
    }

    /**
     * @param Status $status
     * @return static
     */
    public static function forStatus(Status $status): self{
        return new self(null, null, $status->getName(), null, null);
    }

    /**
     * @param string $moderatorId
     * @param string $statusTactic
     * @return static
     */
    public static function fromModeratorProcessing(string $moderatorId, string $statusTactic): self {
        return new self(null,null, null, $moderatorId, $statusTactic);
    }

    /**
     * @param string $statusTactic
     * @return static
     */
    public static function fromStatusTactic(string $status, string $statusTactic): self {
        return new self(null,null, $status, null, $statusTactic);
    }
}