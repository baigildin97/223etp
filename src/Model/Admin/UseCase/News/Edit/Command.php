<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\News\Edit;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Model\Admin\UseCase\News\Edit
 */
class Command
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $subject;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $text;

    /**
     * Command constructor.
     * @param string $id
     * @param string $subject
     * @param string $text
     */
    public function __construct(string $id, string $subject, string $text){
        $this->id = $id;
        $this->subject = $subject;
        $this->text = $text;
    }

    /**
     * @param string $id
     * @param string $subject
     * @param string $text
     * @return Command
     */
    public static function edit(string $id, string $subject, string $text){
        $me = new self($id, $subject, $text);
        $me->id = $id;
        $me->subject = $subject;
        $me->text = $text;

        return $me;
    }
}