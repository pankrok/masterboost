<?php

namespace App\Entity;

use App\Repository\ServersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ServersRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="`servers`")
 * @UniqueEntity(fields={"address"}, message="There is already an server with this address")
 */
class Servers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hostname;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $players;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxplayers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $map;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $game;
    
    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $gameq = [];

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_end_static;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_end_dynamic;
    
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rounds;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $mainpage;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="servers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $uid;

    /**
     * @ORM\OneToOne(targetEntity=Ban::class, mappedBy="sid", cascade={"persist", "remove"})
     */
    private $ban;

    /**
     * @ORM\OneToMany(targetEntity=Vote::class, mappedBy="sid")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $vote;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="sid", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=ServerStatistics::class, mappedBy="server")
     */
    private $serverStatistics;


    public function __construct()
    {
        $this->vote = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->serverStatistics = new ArrayCollection();
    }

    public function __toString(){
        return $this->address . ' - ' . $this->hostname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(?string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getPlayers(): ?int
    {
        return $this->players;
    }

    public function setPlayers(?int $players): self
    {
        $this->players = $players;

        return $this;
    }

    public function getMaxplayers(): ?int
    {
        return $this->maxplayers;
    }

    public function setMaxplayers(?int $maxplayers): self
    {
        $this->maxplayers = $maxplayers;

        return $this;
    }

    public function getMap(): ?string
    {
        return $this->map;
    }

    public function setMap(string $map): self
    {
        $this->map = $map;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getGame(): ?string
    {
        return $this->game;
    }

    public function setGame(string $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->date_create;
    }

    public function setDateCreate(\DateTimeInterface $date_create): self
    {
        $this->date_create = $date_create;

        return $this;
    }

    public function getDateEndStatic(): ?\DateTimeInterface
    {
        return $this->date_end_static;
    }

    public function setDateEndStatic(?\DateTimeInterface $date_end): self
    {
        $this->date_end_static = $date_end;

        return $this;
    }
    
     public function getDateEndDynamic(): ?\DateTimeInterface
    {
        return $this->date_end_dynamic;
    }

    public function setDateEndDynamic(?\DateTimeInterface $date_end): self
    {
        $this->date_end_dynamic = $date_end;

        return $this;
    }

    public function getGameq(): ?array
    {
        return $this->gameq;
    }

    public function setGameq(?array $gameq): self
    {
        $this->gameq = $gameq;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(?\DateTimeInterface $date_update): self
    {
        $this->date_update = $date_update;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(?float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRounds(): ?int
    {
        return $this->rounds;
    }

    public function setRounds(?int $rounds): self
    {
        $this->rounds = $rounds;

        return $this;
    }

    public function getMainpage(): ?float
    {
        return $this->mainpage;
    }

    public function setMainpage(?float $mainpage): self
    {
        $this->mainpage = $mainpage;

        return $this;
    }

    public function getUid(): ?User
    {
        return $this->uid;
    }

    public function setUid(?User $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function getBan(): ?Ban
    {
        return $this->ban;
    }

    public function setBan(Ban $ban): self
    {
        // set the owning side of the relation if necessary
        if ($ban->getSid() !== $this) {
            $ban->setSid($this);
        }

        $this->ban = $ban;

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVote(): Collection
    {
        return $this->vote;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->vote->contains($vote)) {
            $this->vote[] = $vote;
            $vote->setSid($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->vote->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getSid() === $this) {
                $vote->setSid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setSid($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getSid() === $this) {
                $comment->setSid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ServerStatistics[]
     */
    public function getServerStatistics(): Collection
    {
        return $this->serverStatistics;
    }

    public function addServerStatistic(ServerStatistics $serverStatistic): self
    {
        if (!$this->serverStatistics->contains($serverStatistic)) {
            $this->serverStatistics[] = $serverStatistic;
            $serverStatistic->setServer($this);
        }

        return $this;
    }

    public function removeServerStatistic(ServerStatistics $serverStatistic): self
    {
        if ($this->serverStatistics->removeElement($serverStatistic)) {
            // set the owning side to null (unless already changed)
            if ($serverStatistic->getServer() === $this) {
                $serverStatistic->setServer(null);
            }
        }

        return $this;
    }

}
