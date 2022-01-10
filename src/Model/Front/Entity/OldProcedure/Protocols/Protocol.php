<?php
declare(strict_types=1);
namespace App\Model\Front\Entity\OldProcedure\Protocols;

use App\Model\Front\Entity\OldProcedure\OldProcedure;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Protocol
 * @package App\Model\Front\Entity\Procedure\Protocols
 * @ORM\Entity()
 * @ORM\Table(schema="old_records", name="protocols")
 */
class Protocol
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="old_protocol_id")
     */
    private $id;

    /**
     * @var OldProcedure
     * @ORM\ManyToOne(targetEntity="App\Model\Front\Entity\OldProcedure\OldProcedure", inversedBy="files")
     * @ORM\JoinColumn(name="procedure_id", referencedColumnName="id", nullable=false)
     */
    private $procedure;

    /**
     * @var string
     * @ORM\Column(type="text", name="name")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", name="text")
     */
    private $text;

    public function __construct(Id $id, OldProcedure $oldProcedure, string $name, string $text)
    {
        $this->id = $id;
        $this->procedure = $oldProcedure;
        $this->name = $name;
        $this->text = $text;
    }
}