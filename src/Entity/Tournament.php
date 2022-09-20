<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'bigint')]
    private $idTournament;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\Column(type: 'integer')]
    private $prizepool;

    #[ORM\Column(type: 'integer')]
    private $nbplayers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $finalposition;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $win;

    #[ORM\Column(type: 'float')]
    private $buyin;

    #[ORM\OneToMany(mappedBy: 'tournament', targetEntity: Hands::class, orphanRemoval: true)]
    private $hands;

    public function __construct()
    {
        $this->hands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTournament(): ?int
    {
        return $this->idTournament;
    }

    public function setIdTournament(int $idTournament): self
    {
        $this->idTournament = $idTournament;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPrizepool(): ?int
    {
        return $this->prizepool;
    }

    public function setPrizepool(int $prizepool): self
    {
        $this->prizepool = $prizepool;

        return $this;
    }

    public function getNbplayers(): ?int
    {
        return $this->nbplayers;
    }

    public function setNbplayers(int $nbplayers): self
    {
        $this->nbplayers = $nbplayers;

        return $this;
    }

    public function getFinalposition(): ?int
    {
        return $this->finalposition;
    }

    public function setFinalposition(?int $finalposition): self
    {
        $this->finalposition = $finalposition;

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

    /**
     * @return mixed
     */
    public function getBuyin()
    {
        return $this->buyin;
    }

    /**
     * @param mixed $buyin
     */
    public function setBuyin($buyin)
    {
        $this->buyin = $buyin;
    }

    /**
     * @return Collection<int, Hands>
     */
    public function getHands(): Collection
    {
        return $this->hands;
    }

    public function addHand(Hands $hand): self
    {
        if (!$this->hands->contains($hand)) {
            $this->hands[] = $hand;
            $hand->setTournament($this);
        }

        return $this;
    }

    public function removeHand(Hands $hand): self
    {
        if ($this->hands->removeElement($hand)) {
            // set the owning side to null (unless already changed)
            if ($hand->getTournament() === $this) {
                $hand->setTournament(null);
            }
        }

        return $this;
    }


}
