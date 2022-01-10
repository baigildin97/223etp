<?php
declare(strict_types=1);
namespace App\Model\Front\Entity\OldProcedure\Notice;

use App\Model\Front\Entity\OldProcedure\OldProcedure;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Protocol
 * @package App\Model\Front\Entity\OldProcedure\Notice
 * @ORM\Entity()
 * @ORM\Table(schema="old_records", name="notice")
 */
class Notice
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="old_notice_id")
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

    /**
     * Notice constructor.
     * @param Id $id
     * @param OldProcedure $oldProcedure
     * @param string $name
     * @param string $text
     */
    public function __construct(Id $id, OldProcedure $oldProcedure, string $name, string $text)
    {
        $this->id = $id;
        $this->procedure = $oldProcedure;
        $this->name = $name;
        $this->text = $text;
    }
}