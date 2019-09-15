<?php

declare(strict_types=1);

namespace Random\Developer\Jedi\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="\Random\Developer\Jedi\Repository\JediRepository")
 */
class Jedi implements JsonSerializable
{
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(type="string", length=30, nullable=false)
     */
    private $name;

    /**
     * @var int $lightsaberColor
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    private $lightsaberColor;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getLightsaberColor(): int
    {
        return $this->lightsaberColor;
    }

    /**
     * @param int $lightsaberColor
     */
    public function setLightsaberColor(int $lightsaberColor): void
    {
        $this->lightsaberColor = $lightsaberColor;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'lightsaberColor' => $this->getLightsaberColor(),
        ];

        return $data;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return \json_encode($this->toArray());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->jsonSerialize();
    }
}
