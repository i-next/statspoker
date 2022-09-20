<?php

namespace App\Entity;

use App\Repository\CardsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardsRepository::class)]
class Cards
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 2)]
    private $value;

    #[ORM\Column(type: 'string', length: 1)]
    private $color;

    #[ORM\Column(type: 'integer')]
    private $count;

    #[ORM\OneToMany(mappedBy: 'card1', targetEntity: Hands::class)]
    private $hands;

    #[ORM\Column(type: 'integer')]
    private $flop;

    #[ORM\Column(type: 'integer')]
    private $turn;

    #[ORM\Column(type: 'integer')]
    private $river;

    #[ORM\OneToMany(mappedBy: 'id_card1', targetEntity: Main::class)]
    private $mains;

    #[ORM\Column(type: 'integer')]
    private $mycard;

    #[ORM\Column(type: 'integer')]
    private $flopcard;

    #[ORM\Column(type: 'integer')]
    private $turncard;

    #[ORM\Column(type: 'integer')]
    private $rivercars;

    public function __construct()
    {
        $this->hands = new ArrayCollection();
        $this->mains = new ArrayCollection();
    }

    public function __toString() {
        return $this->getValue().$this->getColor();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
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
            $hand->setCard1($this);
        }

        return $this;
    }

    public function removeHand(Hands $hand): self
    {
        if ($this->hands->removeElement($hand)) {
            // set the owning side to null (unless already changed)
            if ($hand->getCard1() === $this) {
                $hand->setCard1(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFlop()
    {
        return $this->flop;
    }

    /**
     * @param mixed $flop
     */
    public function setFlop($flop)
    {
        $this->flop = $flop;
    }

    /**
     * @return mixed
     */
    public function getTurn()
    {
        return $this->turn;
    }

    /**
     * @param mixed $turn
     */
    public function setTurn($turn)
    {
        $this->turn = $turn;
    }

    /**
     * @return mixed
     */
    public function getRiver()
    {
        return $this->river;
    }

    /**
     * @param mixed $river
     */
    public function setRiver($river)
    {
        $this->river = $river;
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
            $main->setIdCard1($this);
        }

        return $this;
    }

    public function removeMain(Main $main): self
    {
        if ($this->mains->removeElement($main)) {
            // set the owning side to null (unless already changed)
            if ($main->getIdCard1() === $this) {
                $main->setIdCard1(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMycard()
    {
        return $this->mycard;
    }

    /**
     * @param mixed $mycard
     */
    public function setMycard($mycard)
    {
        $this->mycard = $mycard;
    }

    /**
     * @return mixed
     */
    public function getFlopcard()
    {
        return $this->flopcard;
    }

    /**
     * @param mixed $flopcard
     */
    public function setFlopcard($flopcard)
    {
        $this->flopcard = $flopcard;
    }

    /**
     * @return mixed
     */
    public function getTurncard()
    {
        return $this->turncard;
    }

    /**
     * @param mixed $turncard
     */
    public function setTurncard($turncard)
    {
        $this->turncard = $turncard;
    }

    /**
     * @return mixed
     */
    public function getRivercars()
    {
        return $this->rivercars;
    }

    /**
     * @param mixed $rivercars
     */
    public function setRivercars($rivercars)
    {
        $this->rivercars = $rivercars;
    }


}
