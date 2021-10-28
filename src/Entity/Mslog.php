<?php

namespace App\Entity;

use App\Repository\MslogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MslogRepository::class)
 */
class Mslog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timeyear;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timemonth;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timeday;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timehour;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timeminute;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timesecond;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $port;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeyear(): ?string
    {
        return $this->timeyear;
    }

    public function setTimeyear(?string $timeyear): self
    {
        $this->timeyear = $timeyear;

        return $this;
    }

    public function getTimemonth(): ?string
    {
        return $this->timemonth;
    }

    public function setTimemonth(?string $timemonth): self
    {
        $this->timemonth = $timemonth;

        return $this;
    }

    public function getTimeday(): ?string
    {
        return $this->timeday;
    }

    public function setTimeday(?string $timeday): self
    {
        $this->timeday = $timeday;

        return $this;
    }

    public function getTimehour(): ?string
    {
        return $this->timehour;
    }

    public function setTimehour(?string $timehour): self
    {
        $this->timehour = $timehour;

        return $this;
    }

    public function getTimeminute(): ?string
    {
        return $this->timeminute;
    }

    public function setTimeminute(?string $timeminute): self
    {
        $this->timeminute = $timeminute;

        return $this;
    }

    public function getTimesecond(): ?string
    {
        return $this->timesecond;
    }

    public function setTimesecond(?string $timesecond): self
    {
        $this->timesecond = $timesecond;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getPort(): ?string
    {
        return $this->port;
    }

    public function setPort(?string $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
