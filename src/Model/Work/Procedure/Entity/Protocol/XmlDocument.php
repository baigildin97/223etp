<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Protocol;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class XmlDocument
 * @package App\Model\Work\Procedure\Entity\Protocol
 * @ORM\Embeddable()
 */
class XmlDocument
{
    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $file;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $hashOrganizer;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $signOrganizer;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="signed_at_organizer")
     */
    private $signedAtOrganizer;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $certificateThumbprintOrganizer;


    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $hashParticipant;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $signParticipant;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="signed_at_participant", nullable=true)
     */
    private $signedAtParticipant;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $certificateThumbprintParticipant;

    /**
     * @param string $file
     * @param string $hashOrganizer
     * @param string $signOrganizer
     * @param string $certificateThumbprintOrganizer
     * @param \DateTimeImmutable $signedAtOrganizer
     * @return XmlDocument
     */
    public static function signedOrganizer(
        string $file,
        string $hashOrganizer,
        string $signOrganizer,
        string $certificateThumbprintOrganizer,
        \DateTimeImmutable $signedAtOrganizer
    ){
        $instance = new self();
        $instance->file = $file;
        $instance->hashOrganizer = $hashOrganizer;
        $instance->signOrganizer = $signOrganizer;
        $instance->certificateThumbprintOrganizer = $certificateThumbprintOrganizer;
        $instance->signedAtOrganizer = $signedAtOrganizer;
        return $instance;
    }


    /**
     * @param string $hashParticipant
     * @param string $signParticipant
     * @param string $certificateThumbprintParticipant
     * @param \DateTimeImmutable $signedAtParticipant
     * @return XmlDocument
     */
    public function signedParticipant(
        string $hashParticipant,
        string $signParticipant,
        string $certificateThumbprintParticipant,
        \DateTimeImmutable $signedAtParticipant
    ){
        if(!$this->getHashOrganizer()){
            throw new \DomainException("Документ уже подписан победителем");
        }
        $this->hashParticipant = $hashParticipant;
        $this->signParticipant = $signParticipant;
        $this->certificateThumbprintParticipant = $certificateThumbprintParticipant;
        $this->signedAtParticipant = $signedAtParticipant;
    }


    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getHashOrganizer(): string
    {
        return $this->hashOrganizer;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getSignedAtOrganizer(): \DateTimeImmutable
    {
        return $this->signedAtOrganizer;
    }

    /**
     * @return string
     */
    public function getHashParticipant(){
        return $this->hashParticipant;
    }


}