<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\EditRepresentative;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     */
    public $profile_id;

    /**
     * @Assert\NotBlank()
     */
    public $position;

    /**
     * @Assert\NotBlank()
     */
    public $confirmingDocument;

    /**
     * @Assert\NotBlank()
     */
    public $phone;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\Role(type="digit")
     * @Assert\Length(min="4", max="4")
     * @Assert\NotBlank()
     */
    public $passportSeries;

    /**
     * @Assert\Role(type="digit")
     * @Assert\Length(min="6", max="6")
     * @Assert\NotBlank()
     */
    public $passportNumber;

    /**
     * @Assert\NotBlank()
     */
    public $passportIssuer;

    /**
     * @Assert\NotBlank()
     */
    public $passportIssuanceDate;

    /**
     * @Assert\NotBlank()
     */
    public $citizenship;

    /**
     * @Assert\NotBlank()
     */
    public $unitCode;

    /**
     * @Assert\NotBlank()
     */
    public $birthDate;

    /**
     * @var string
     * @Assert\Length(min=5, max=100)
     */
    public $fio;

    public function __construct() {}

    public static function toEditReprInfo(string $position, string $confirmingDocument, string $phone,
                                          string $email, ?string $series, ?string $number, ?string $issuer,
                                          \DateTimeImmutable $issuanceDate, ?string $unitCode, ?string $citizenship,
                                          \DateTimeImmutable $birthday, ?string $fio): self
    {
        $me = new self();

        $me->position = $position;
        $me->confirmingDocument = $confirmingDocument;
        $me->phone = $phone;
        $me->email = $email;
        $me->passportSeries = $series;
        $me->passportNumber = $number;
        $me->passportIssuer = $issuer;
        $me->passportIssuanceDate = $issuanceDate;
        $me->unitCode = $unitCode;
        $me->citizenship = $citizenship;
        $me->birthDate = $birthday;
        $me->fio = $fio;

        return $me;
    }
}