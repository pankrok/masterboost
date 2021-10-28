<?php

namespace App\Entity;

use App\Repository\ServerStatisticsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServerStatisticsRepository::class)
 */
class ServerStatistics
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Servers::class, inversedBy="serverStatistics")
     */
    private $server;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $players;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServer(): ?Servers
    {
        return $this->server;
    }

    public function setServer(?Servers $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPlayers(): ?int
    {
        return $this->players;
    }

    public function setPlayers(int $players): self
    {
        $this->players = $players;

        return $this;
    }
}
