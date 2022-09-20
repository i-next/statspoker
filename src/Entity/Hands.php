<?php

namespace App\Entity;

use App\Repository\HandsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HandsRepository::class)]
class Hands
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'bigint')]
    private $hand_id;

    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'hands')]
    #[ORM\JoinColumn(nullable: false)]
    private $tournament;

    #[ORM\ManyToOne(targetEntity: Cards::class, inversedBy: 'hands')]
    #[ORM\JoinColumn(nullable: false)]
    private $card1;

    #[ORM\ManyToOne(targetEntity: Cards::class, inversedBy: 'hands')]
    #[ORM\JoinColumn(nullable: false)]
    private $card2;

    #[ORM\Column(type: 'array')]
    private $flop = [];

    #[ORM\ManyToOne(targetEntity: Cards::class)]
    private $turn;

    #[ORM\ManyToOne(targetEntity: Cards::class, inversedBy: 'hands')]
    private $river;

    #[ORM\Column(type: 'array')]
    private $players = [];

    #[ORM\Column(type: 'boolean')]
    private $win;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHandId(): ?int
    {
        return $this->hand_id;
    }

    public function setHandId(int $hand_id): self
    {
        $this->hand_id = $hand_id;

        return $this;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): self
    {
        $this->tournament = $tournament;

        return $this;
    }

    public function getCard1(): ?Cards
    {
        return $this->card1;
    }

    public function setCard1(?Cards $card1): self
    {
        $this->card1 = $card1;

        return $this;
    }

    public function getCard2(): ?Cards
    {
        return $this->card2;
    }

    public function setCard2(?Cards $card2): self
    {
        $this->card2 = $card2;

        return $this;
    }

    public function getFlop(): ?array
    {
        return $this->flop;
    }

    public function setFlop(array $flop): self
    {
        $this->flop = $flop;

        return $this;
    }

    public function getTurn(): ?Cards
    {
        return $this->turn;
    }

    public function setTurn(?Cards $turn): self
    {
        $this->turn = $turn;

        return $this;
    }

    public function getRiver(): ?Cards
    {
        return $this->river;
    }

    public function setRiver(?Cards $river): self
    {
        $this->river = $river;

        return $this;
    }

    public function getPlayers(): ?array
    {
        return $this->players;
    }

    public function setPlayers(array $players): self
    {
        $this->players = $players;

        return $this;
    }

    public function getWin(): ?bool
    {
        return $this->win;
    }

    public function setWin(bool $win): self
    {
        $this->win = $win;

        return $this;
    }
}
