<?php

namespace App\Entity;

use App\Repository\TournoiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournoiRepository::class)]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $identifiant;

    #[ORM\Column(type: 'float')]
    private $buyin;

    #[ORM\Column(type: 'smallint')]
    private $nbplayer;

    #[ORM\Column(type: 'float')]
    private $prizepool;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $win;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $position;

    #[ORM\Column(type: 'date', nullable: true)]
    private $date;

    #[ORM\OneToMany(mappedBy: 'id_tournoi', targetEntity: Main::class, orphanRemoval: true)]
    private $mains;

    #[ORM\Column(type: 'boolean')]
    private $ticket;

    #[ORM\Column(type: 'float')]
    private $money;

    public function __construct()
    {
        $this->mains = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant($identifiant): self
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

    public function getNbplayer(): ?int
    {
        return $this->nbplayer;
    }

    public function setNbplayer(int $nbplayer): self
    {
        $this->nbplayer = $nbplayer;

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

    public function getWin(): ?bool
    {
        return $this->win;
    }

    public function setWin(?bool $win): self
    {
        $this->win = $win;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, Main>
     */
    public function getMains(): Collection
    {
        return $this->mains;
    }

    public function addMain(Main $main): self
    {
        if (!$this->mains->contains($main)) {
            $this->mains[] = $main;
            $main->setIdTournoi($this);
        }

        return $this;
    }

    public function removeMain(Main $main): self
    {
        if ($this->mains->removeElement($main)) {
            // set the owning side to null (unless already changed)
            if ($main->getIdTournoi() === $this) {
                $main->setIdTournoi(null);
            }
        }

        return $this;
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
