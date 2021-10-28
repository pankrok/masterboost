<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingsRepository::class)
 */
class Settings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $uniqie_ips;

    /**
     * @ORM\Column(type="text")
     */
    private $all_ips;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUniqieIps(): ?string
    {
        return $this->uniqie_ips;
    }

    public function setUniqieIps(string $uniqie_ips): self
    {
        $this->uniqie_ips = $uniqie_ips;

        return $this;
    }

    public function getAllIps(): ?string
    {
        return $this->all_ips;
    }

    public function setAllIps(string $all_ips): self
    {
        $this->all_ips = $all_ips;

        return $this;
    }
}
