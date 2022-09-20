<?php

namespace App\Entity;

use App\Repository\JoueurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JoueurRepository::class)]
class Joueur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $pseudo;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $hand_win;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $tour_win;

    #[ORM\OneToMany(mappedBy: 'id_player1', targetEntity: Main::class)]
    private $mains;

    public function __construct()
    {
        $this->mains = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getHandWin(): ?string
    {
        return $this->hand_win;
    }

    public function setHandWin(?string $hand_win): self
    {
        $this->hand_win = $hand_win;

        return $this;
    }

    public function getTourWin(): ?string
    {
        return $this->tour_win;
    }

    public function setTourWin(?string $tour_win): self
    {
        $this->tour_win = $tour_win;

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
            $main->setIdPlayer1($this);
        }

        return $this;
    }

    public function removeMain(Main $main): self
    {
        if ($this->mains->removeElement($main)) {
            // set the owning side to null (unless already changed)
            if ($main->getIdPlayer1() === $this) {
                $main->setIdPlayer1(null);
            }
        }

        return $this;
    }
}
