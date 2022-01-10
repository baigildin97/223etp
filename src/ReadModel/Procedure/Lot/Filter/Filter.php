<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Lot\Filter;


class Filter
{
    public $procedureId;
    public $startDateOfApplications;
    public $applicationClosingDate;
    public $statusSummingUpApplications;

    public function __construct(?string $procedureId, ?\DateTimeImmutable $startDateOfApplications, ?\DateTimeImmutable $applicationClosingDate, ?\DateTimeImmutable $statusSummingUpApplications) {
        $this->procedureId = $procedureId;
        $this->startDateOfApplications = $startDateOfApplications;
        $this->applicationClosingDate = $applicationClosingDate;
        $this->statusSummingUpApplications = $statusSummingUpApplications;
    }

    /**
     * @param string $procedureId
     * @return static
     */
    public static function fromProcedure(string $procedureId): self{
        return new self($procedureId, null, null, null);
    }

    /**
     * @param \DateTimeImmutable $startDateOfApplications
     * @return static
     */
    public static function startDateOfApplications(\DateTimeImmutable $startDateOfApplications): self{
        return new self(null, $startDateOfApplications, null, null);
    }

    /**
     * @param \DateTimeImmutable $applicationClosingDate
     * @return static
     */
    public static function applicationClosingDate(\DateTimeImmutable $applicationClosingDate): self {
        return new self(null, null, $applicationClosingDate, null);
    }

    /**
     * @param \DateTimeImmutable $statusSummingUpApplications
     * @return static
     */
    public static function statusSummingUpApplications(\DateTimeImmutable $statusSummingUpApplications): self {
        return new self(null, null, null, $statusSummingUpApplications);
    }

}