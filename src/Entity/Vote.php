<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VoteRepository::class)
 */
class Vote
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $vote_ip;

    /**
     * @ORM\ManyToOne(targetEntity=Servers::class, inversedBy="vote")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sid;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="votes")
     */
    private $user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVoteIp(): ?string
    {
        return $this->vote_ip;
    }

    public function setVoteIp(string $vote_ip): self
    {
        $this->vote_ip = $vote_ip;

        return $this;
    }

    public function getSid(): ?Servers
    {
        return $this->sid;
    }

    public function setSid(?Servers $sid): self
    {
        $this->sid = $sid;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
