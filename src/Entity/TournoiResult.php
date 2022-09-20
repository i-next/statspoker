<?php

namespace App\Entity;

use App\Repository\TournoiResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournoiResultRepository::class)]
class TournoiResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $identifiant;

    #[ORM\Column(type: 'float')]
    private $buyin;

    #[ORM\Column(type: 'float')]
    private $prizepool;

    #[ORM\Column(type: 'integer')]
    private $win;

    #[ORM\Column(type: 'integer')]
    private $nbtour;

    #[ORM\Column(type: 'boolean')]
    private $ticket;

    #[ORM\Column(type: 'float')]
    private $money;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getBuyin(): ?float
    {
        return $this->buyin;
    }

    public function setBuyin(float $buyin): self
    {
        $this->buyin = $buyin;

        return $this;
    }

    public function getPrizepool(): ?float
    {
        return $this->prizepool;
    }

    public function setPrizepool(float $prizepool): self
    {
        $this->prizepool = $prizepool;

        return $this;
    }

    public function getWin(): ?int
    {
        return $this->win;
    }

    public function setWin(int $win): self
    {
        $this->win = $win;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNbtour()
    {
        return $this->nbtour;
    }

    /**
     * @param mixed $nbtour
     */
    public function setNbtour($nbtour)
    {
        $this->nbtour = $nbtour;
    }

    public function getTicket(): ?bool
    {
        return $this->ticket;
    }

    public function setTicket(bool $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @param mixed $money
     */
    public function setMoney($money)
    {
        $this->money = $money;
    }


}
