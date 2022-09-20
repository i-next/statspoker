<?php


namespace App\Service;


use App\Entity\Hands;
use App\Entity\Players;
use App\Repository\CardsRepository;
use App\Repository\HandsRepository;
use App\Repository\PlayersRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImportService
{
    private $entityManager;

    private $tournamentRepository;

    private $cardsRepository;

    private $playersRepository;

    private $handRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TournamentRepository $tournamentRepository,
        CardsRepository $cardsRepository,
        PlayersRepository $playersRepository,
        HandsRepository $handRepository)
    {
        $this->entityManager = $entityManager;
        $this->tournamentRepository = $tournamentRepository;
        $this->cardsRepository = $cardsRepository;
        $this->playersRepository = $playersRepository;
        $this->handRepository = $handRepository;
    }

    public function handsBase(string $data, Hands $hand)
    {
        $players = [];

        if (!isset($player1bis) && $player1 = $this->get_string_between($data, 'Place 1: ', ' (')) {
            if ($player1 !== 'psychoman59') {
                $player1bis = $player1;
                $player1Tourn = $this->playersRepository->findOneBy(['Pseudo' => $player1]);
                if (!$player1Tourn) {
                    $player1Tourn = new Players();
                    $player1Tourn->setPseudo(utf8_encode($player1));
                    $player1Tourn->setWinTour(0);
                    $player1Tourn->setWinHand(0);
                    $this->entityManager->persist($player1Tourn);
                    $this->entityManager->flush();
                }
                $players[] = $player1Tourn;
            }
        }
        if (!isset($player2bis) && $player2 = $this->get_string_between($data, 'Place 2: ', ' (')) {

            if ($player2 !== 'psychoman59') {
                $player2bis = $player2;
                $player2Tourn = $this->playersRepository->findOneBy(['Pseudo' => $player2]);
                if (!$player2Tourn) {
                    $player2Tourn = new Players();
                    $player2Tourn->setPseudo(utf8_encode($player2));
                    $player2Tourn->setWinTour(0);
                    $player2Tourn->setWinHand(0);
                    $this->entityManager->persist($player2Tourn);
                    $this->entityManager->flush();
                }
                $players[] = $player2Tourn;
            }
        }

        if (!isset($player3bis) && $player3 = $this->get_string_between($data, 'Place 3: ', ' (')) {
            if ($player3 !== 'psychoman59') {
                $player3bis = $player3;
                $player3Tourn = $this->playersRepository->findOneBy(['Pseudo' => $player3]);
                if (!$player3Tourn) {
                    $player3Tourn = new Players();
                    $player3Tourn->setPseudo(utf8_encode($player3));
                    $player3Tourn->setWinTour(0);
                    $player3Tourn->setWinHand(0);
                    $this->entityManager->persist($player3Tourn);
                    $this->entityManager->flush();
                }
                $players[] = $player3Tourn;
            }
        }

        if (empty($hand->getPlayers()) && count($players) > 0) {
            $hand->setPlayers($players);
        }

        if ($cards = $this->get_string_between($data, 'psychoman59 [', ']')) {
            $cardsDetails = explode(' ', $cards);
            $firstCard = $this->cardsRepository->findOneBy(['value' => substr($cardsDetails[0], 0, 1), 'color' => substr($cardsDetails[0], 1, 1)]);
            $secondCard = $this->cardsRepository->findOneBy(['value' => substr($cardsDetails[1], 0, 1), 'color' => substr($cardsDetails[1], 1, 1)]);
            $firstCard->setCount($firstCard->getCount() + 1);
            $secondCard->setCount($secondCard->getCount() + 1);

            $this->entityManager->persist($firstCard);
            $this->entityManager->persist($secondCard);
            $this->entityManager->flush();
            $hand->setCard1($firstCard);
            $hand->setCard2($secondCard);
        }
dump($data);
        if ($this->get_string_between($data, 'a remport', 'lors du pot')) {
            $handWinner = strtok($data, " ");
           // dump($players,$handWinner,$player1,$player2,$player3);die;
            switch ($handWinner) {
                case 'psychoman59':
                    $hand->setWin(1);
                    break;
                case $player1:
                    $hand->setWin(0);
                    $player1Tourn->setWinHand($player1Tourn->getWinHand() + 1);
                    $this->entityManager->persist($player1Tourn);
                    break;
                case $player2:
                    $hand->setWin(0);
                    $player2Tourn->setWinHand($player2Tourn->getWinHand() + 1);
                    $this->entityManager->persist($player2Tourn);
                    break;
                case $player3:
                    $hand->setWin(0);
                    $player3Tourn->setWinHand($player3Tourn->getWinHand() + 1);
                    $this->entityManager->persist($player3Tourn);
                    break;
            }
            $this->entityManager->persist($hand);
            $this->entityManager->flush();
        }

        if ($this->get_string_between($data, 'remporte ', 'tournoi')) {
            $this->entityManager->persist($hand);
            $this->entityManager->flush();
            unset($player1bis, $player2bis, $player3bis);
        }

        if ($this->get_string_between($data, 'FL ', 'P')) {
            $flop = $this->get_string_between($data, '[FL ]', ']');
            $cardsFlop = explode(" ", $flop);
            dump($cardsFlop);
            foreach ($cardsFlop as $cardFlop) {
                $card = str_split($cardFlop);
                $cardClass = $this->cardsRepository->findOneBy(['value' => $card[0], 'color' => $card[1]]);
                $cardClass->setFlop($cardClass->getFlop() + 1);
                $cardsFlop[] = $cardClass;
                $this->entityManager->persist($cardClass);
            }
            $hand->setFlop($cardsFlop);
            return $hand;
        }

        if ($turn = $this->get_string_between($data, 'TO', 'NANT')) {
            $turnCard = $this->get_string_between($data, '] [', ']');
            $card = str_split($turnCard);
            $cardClass = $this->cardsRepository->findOneBy(['value' => $card[0], 'color' => $card[1]]);
            $cardClass->setTurn($cardClass->getTurn() + 1);
            $this->entityManager->persist($cardClass);
            $hand->setTurn($cardClass);
            return $hand;
        }

        if ($river = $this->get_string_between($data, 'RE *** [', ']')) {
            $riverCard = $this->get_string_between($data, '] [', ']');
            $card = str_split($riverCard);
            $cardClass = $this->cardsRepository->findOneBy(['value' => $card[0], 'color' => $card[1]]);
            $cardClass->setRiver($cardClass->getRiver() + 1);
            $this->entityManager->persist($cardClass);
            $hand->setRiver($cardClass);
            return $hand;
        }

        if ($this->get_string_between($data, 'est', 'ouch')) {
            $player = $this->get_string_between($data, ': ', ' (');
            if ($player !== 'psychoman59') {
                $playerfold = $this->playersRepository->findOneBy(['Pseudo' => $player]);
                $playerfold->setWinHand($playerfold->getWinHand() - 1);
            } else {
                $hand->setWin(0);
                $this->entityManager->persist($hand);
                $this->entityManager->flush();
            }
        }
    }


    public function handsDetails(string $data, Hands $hand)
    {



        dump($data, $this->get_string_between($data, 'FL ', 'P'));


    }

    public function get_string_between($string, $start, $end)
    {
        if (str_contains($string, $end)) {
            if ($start !== '') {
                $string = ' ' . $string;
                $ini = strpos($string, $start);
                if ($ini == 0) return '';

                $ini += strlen($start);

                $len = strpos($string, $end, $ini) - $ini;
                return substr($string, $ini, $len);
            } else {
                return substr($string, 0, -strlen($end));
            }
        } else {
            return false;
        }
    }

}