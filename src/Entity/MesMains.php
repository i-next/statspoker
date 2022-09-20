<?php

namespace App\Entity;

use App\Repository\MesMainsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MesMainsRepository::class)]
class MesMains
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Cards::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $id_card1;

    #[ORM\ManyToOne(targetEntity: Cards::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $id_card2;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $count;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $win;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCount(): ?string
    {
        return $this->count;
    }

    public function setCount(?string $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getWin(): ?string
    {
        return $this->win;
    }

    public function setWin(?string $win): self
    {
        $this->win = $win;

        return $this;
    }
}
