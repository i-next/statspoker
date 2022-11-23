<?php

namespace App\Entity;

use App\Repository\MainRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MainRepository::class)]
class Main
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Tournoi::class, inversedBy: 'mains')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_tournoi;

    #[ORM\ManyToOne(targetEntity: Cards::class, inversedBy: 'mains')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_card1;

    #[ORM\ManyToOne(targetEntity: Cards::class, inversedBy: 'mains')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_card2;

    #[ORM\ManyToOne(targetEntity: Cards::class)]
    private $id_flop1;

    #[ORM\ManyToOne(targetEntity: Cards::class)]
    private $id_flop2;

    #[ORM\ManyToOne(targetEntity: Cards::class)]
    private $id_flop3;

    #[ORM\ManyToOne(targetEntity: Cards::class)]
    private $id_turn;

    #[ORM\ManyToOne(targetEntity: Cards::class)]
    private $id_river;

    #[ORM\ManyToOne(targetEntity: Joueur::class, inversedBy: 'mains')]
    #[ORM\JoinColumn(nullable: true)]
    private $id_player1;

    #[ORM\ManyToOne(targetEntity: Joueur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $id_player2;

    #[ORM\ManyToOne(targetEntity: Joueur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $id_player3;

    #[ORM\ManyToOne(targetEntity: Joueur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $id_player4;

    #[ORM\ManyToOne(targetEntity: Joueur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $id_player5;

    #[ORM\ManyToOne(targetEntity: Joueur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $id_player6;

    #[ORM\ManyToOne(targetEntity: Joueur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $id_player7;

    #[ORM\ManyToOne(targetEntity: Joueur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $id_player8;

    #[ORM\ManyToOne(targetEntity: Joueur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $id_player9;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $win;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTournoi(): ?Tournoi
    {
        return $this->id_tournoi;
    }

    public function setIdTournoi(?Tournoi $id_tournoi): self
    {
        $this->id_tournoi = $id_tournoi;

        return $this;
    }

    public function getIdCard1(): ?Cards
    {
        return $this->id_card1;
    }

    public function setIdCard1(?Cards $id_card1): self
    {
        $this->id_card1 = $id_card1;

        return $this;
    }

    public function getIdCard2(): ?Cards
    {
        return $this->id_card2;
    }

    public function setIdCard2(?Cards $id_card2): self
    {
        $this->id_card2 = $id_card2;

        return $this;
    }

    public function getIdFlop1(): ?Cards
    {
        return $this->id_flop1;
    }

    public function setIdFlop1(?Cards $id_flop1): self
    {
        $this->id_flop1 = $id_flop1;

        return $this;
    }

    public function getIdFlop2(): ?Cards
    {
        return $this->id_flop2;
    }

    public function setIdFlop2(?Cards $id_flop2): self
    {
        $this->id_flop2 = $id_flop2;

        return $this;
    }

    public function getIdFlop3(): ?Cards
    {
        return $this->id_flop3;
    }

    public function setIdFlop3(?Cards $id_flop3): self
    {
        $this->id_flop3 = $id_flop3;

        return $this;
    }

    public function getIdTurn(): ?Cards
    {
        return $this->id_turn;
    }

    public function setIdTurn(?Cards $id_turn): self
    {
        $this->id_turn = $id_turn;

        return $this;
    }

    public function getIdRiver(): ?Cards
    {
        return $this->id_river;
    }

    public function setIdRiver(?Cards $id_river): self
    {
        $this->id_river = $id_river;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdPlayer1()
    {
        return $this->id_player1;
    }

    /**
     * @param mixed $id_player1
     */
    public function setIdPlayer1($id_player1)
    {
        $this->id_player1 = $id_player1;
    }

    /**
     * @return mixed
     */
    public function getIdPlayer2()
    {
        return $this->id_player2;
    }

    /**
     * @param mixed $id_player2
     */
    public function setIdPlayer2($id_player2)
    {
        $this->id_player2 = $id_player2;
    }

    /**
     * @return mixed
     */
    public function getIdPlayer3()
    {
        return $this->id_player3;
    }

    /**
     * @param mixed $id_player3
     */
    public function setIdPlayer3($id_player3)
    {
        $this->id_player3 = $id_player3;
    }

    /**
     * @return mixed
     */
    public function getIdPlayer4()
    {
        return $this->id_player4;
    }

    /**
     * @param mixed $id_player4
     */
    public function setIdPlayer4($id_player4)
    {
        $this->id_player4 = $id_player4;
    }

    /**
     * @return mixed
     */
    public function getIdPlayer5()
    {
        return $this->id_player5;
    }

    /**
     * @param mixed $id_player5
     */
    public function setIdPlayer5($id_player5)
    {
        $this->id_player5 = $id_player5;
    }

    /**
     * @return mixed
     */
    public function getIdPlayer6()
    {
        return $this->id_player6;
    }

    /**
     * @param mixed $id_player6
     */
    public function setIdPlayer6($id_player6)
    {
        $this->id_player6 = $id_player6;
    }

    /**
     * @return mixed
     */
    public function getIdPlayer7()
    {
        return $this->id_player7;
    }

    /**
     * @param mixed $id_player7
     */
    public function setIdPlayer7($id_player7)
    {
        $this->id_player7 = $id_player7;
    }

    /**
     * @return mixed
     */
    public function getIdPlayer8()
    {
        return $this->id_player8;
    }

    /**
     * @param mixed $id_player8
     */
    public function setIdPlayer8($id_player8)
    {
        $this->id_player8 = $id_player8;
    }

    /**
     * @return mixed
     */
    public function getIdPlayer9()
    {
        return $this->id_player9;
    }

    /**
     * @param mixed $id_player9
     */
    public function setIdPlayer9($id_player9)
    {
        $this->id_player9 = $id_player9;
    }

    /**
     * @return mixed
     */
    public function getWin()
    {
        return $this->win;
    }

    /**
     * @param mixed $win
     */
    public function setWin($win)
    {
        $this->win = $win;
    }


}
