<?php
declare(strict_types=1);
namespace App\Model\Admin\Entity\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Settings
 * @package App\Model\Work\Entity\Admin\Settings
 * @ORM\Entity()
 * @ORM\Table(name="settings")
 */
class Settings
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="settings_id")
     */
    public $id;

    /**
     * @var Key
     * @ORM\Column(type="settings_key_type", unique=true)
     */
    public $key;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    public $value;

    /**
     * Settings constructor.
     * @param Id $id
     * @param Key $key
     * @param string $value
     */
    public function __construct(Id $id, Key $key, string $value) {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Key
     */
    public function getKey(): Key
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function edit(string $value): void {
        $this->value = $value;
    }

    /**
     * @param Key $key
     * @param string $value
     */
    public function update(Key $key, string $value) {
        $this->key = $key;
        $this->value = $value;
    }
}