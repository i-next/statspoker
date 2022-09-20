<?php

namespace App\Entity;

use App\Repository\PlayersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayersRepository::class)]
class Players
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Pseudo;

    #[ORM\Column(type: 'integer')]
    private $win_tour;

    #[ORM\Column(type: 'integer')]
    private $win_hand;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->Pseudo;
    }

    public function setPseudo(string $Pseudo): self
    {
        $this->Pseudo = $Pseudo;

        return $this;
    }

    public function getWinTour(): ?int
    {
        return $this->win_tour;
    }

    public function setWinTour(int $win_tour): self
    {
        $this->win_tour = $win_tour;

        return $this;
    }

    public function getWinHand(): ?int
    {
        return $this->win_hand;
    }

    public function setWinHand(int $win_hand): self
    {
        $this->win_hand = $win_hand;

        return $this;
    }
}
