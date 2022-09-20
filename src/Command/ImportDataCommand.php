<?php

namespace App\Command;

use SplFileObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Finder;
use App\Entity\Tournament;
use App\Entity\Hands;
use App\Entity\Players;
use App\Repository\TournamentRepository;
use App\Repository\CardsRepository;
use App\Repository\PlayersRepository;
use App\Repository\HandsRepository;
use App\Repository\ResultRepository;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use App\Service\ImportService;

#[AsCommand(
name:
'app:import-data',
    description: 'Import Data',
    hidden: false,
    aliases: ['app:import-data']
)]
class ImportDataCommand extends Command
{
    private $entityManager;

    private $tournamentRepository;

    private $cardsRepository;

    private $playersRepository;

    private $importService;

    private $handsRepository;

    private $resultRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                TournamentRepository $tournamentRepository,
                                CardsRepository $cardsRepository,
                                PlayersRepository $playersRepository,
                                ImportService $importService,
                                ResultRepository $resultRepository,
HandsRepository $handsRepository)
    {
        $this->entityManager = $entityManager;
        $this->tournamentRepository = $tournamentRepository;
        $this->cardsRepository = $cardsRepository;
        $this->playersRepository = $playersRepository;
        $this->importService = $importService;
        $this->resultRepository = $resultRepository;
        $this->handsRepository = $handsRepository;
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $finder = new Finder();
        $finder->files()->in('datasrc/psychoman59')->name('TS*.txt');

        foreach ($finder as $file) {
            $fileData = new SplFileObject($file);
            $idTournament = 0;
            $buyin = 0;
            $nbPplayer = 0;
            $prizePool = 0;
            $dateTournament = new \DateTime();
            $dateTournament->setTimestamp($fileData->getCTime());
            $finalPosition = 0;
            $win = false;
            while (!$fileData->eof()) {
                $data = preg_replace("/\r|\n/", "", $fileData->fgets());
                $data = trim($data, '"');

                if ($this->get_string_between($data, '#', ',')) {
                    $idTournament = (int)$this->get_string_between($data, '#', ',');
                }
                if ($this->get_string_between($data, 'Buy-in : ', ' EUR')) {
                    $buyin = $this->get_string_between($data, 'Buy-in : ', ' EUR');
                    $arraybuyin = explode("/", $buyin);
                    $buyin = str_replace('€', '', $arraybuyin[0]) + (double)str_replace('€', '', $arraybuyin[1]);

                }
                if ($this->get_string_between($data, '', ' joueurs')) {
                    $nbPplayer = (int)$this->get_string_between($data, '', ' joueurs');
                }
                if ($this->get_string_between($data, 'Dotation totale : €', ' EUR ')) {
                    $prizePool = (int)$this->get_string_between($data, 'Dotation totale : €', ' EUR ');
                }
                if ($this->get_string_between($data, '  1: ', ' (') === 'psychoman59') {
                    $finalPosition = 1;
                    $win = true;
                }
                if ($this->get_string_between($data, '  2: ', ' (') === 'psychoman59') {
                    $finalPosition = 2;
                }
                if ($this->get_string_between($data, '  3: ', ' (') === 'psychoman59') {
                    $finalPosition = 3;
                }
                if ($this->get_string_between($data, '  4: ', ' (') === 'psychoman59') {
                    $finalPosition = 4;
                }
                if ($this->get_string_between($data, '  5: ', ' (') === 'psychoman59') {
                    $finalPosition = 5;
                }
                if ($this->get_string_between($data, '  6: ', ' (') === 'psychoman59') {
                    $finalPosition = 6;
                }
                if ($this->get_string_between($data, '  7: ', ' (') === 'psychoman59') {
                    $finalPosition = 7;
                }
                if ($this->get_string_between($data, '  8: ', ' (') === 'psychoman59') {
                    $finalPosition = 8;
                }
                if ($this->get_string_between($data, '  9: ', ' (') === 'psychoman59') {
                    $finalPosition = 9;
                }

            }
            $oldTournament = $this->tournamentRepository->findOneBy(['idTournament' => $idTournament]);
            if (!$oldTournament) {
                $tournament = new Tournament();
                $tournament->setDate($dateTournament);
                $tournament->setBuyin(floatval($buyin));
                $tournament->setFinalposition($finalPosition);
                $tournament->setIdTournament($idTournament);
                $tournament->setNbplayers($nbPplayer);
                $tournament->setPrizepool($prizePool);
                $tournament->setWin($win);
                $this->entityManager->persist($tournament);
                $this->entityManager->flush();
                $io->success('Ajout du tournoi id: ' . $idTournament);
            }
            $filesystem = new Filesystem();
            $path = $fileData->getPath() . '/';
            $pathArchives = $path . '/../archives/archives';
            $filename = $fileData->getFilename();
            $filesystem->rename($path . $filename, $pathArchives . $filename);
        }
        $this->importHands();

        return Command::SUCCESS;
    }

    public function importHands()
    {

        $finder = new Finder();
        $finder->files()->in('datasrc/psychoman59')->name('HH*.txt');
        foreach ($finder as $file) {
            $fileData = new SplFileObject($file);
            $idTournament = 0;
            $players = [];
            $player1 = '';
            $player2 = '';
            $player3 = '';
            while (!$fileData->eof()) {
                $data = preg_replace("/\r|\n/", "", $fileData->fgets());
                $data = trim($data, '"');
                if ($idTournament === 0) {
                    if ($idTournament = (int)$this->get_string_between($data, '#', ',')) {
                        $tournament = $this->tournamentRepository->findOneBy(['idTournament' => $idTournament]);
                    }
                }
                if (isset($tournament)) {
                    if ($idHand = $this->get_string_between($data, 'PokerStars n°', ' : Tournoi')) {
                        $hand = $this->handsRepository->findOneBy(['hand_id' => ltrim($idHand, $idHand[0])]);

                        if(is_null($hand)){
                            $hand = new Hands();
                            $hand->setHandId($idHand);
                        }
                        $hand->setTournament($tournament);

                    }
                    if($hand){


                        if ($this->get_string_between($data, 'Place 1: ', ' (')) {
                            $player1 = $this->get_string_between($data, 'Place 1: ', ' (');
                            //dump('player1:'.$player1);
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
                        if ($this->get_string_between($data, 'Place 2: ', ' (')) {
                            $player2 = $this->get_string_between($data, 'Place 2: ', ' (');
                            //dump('player2:'.$player2);
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

                        if ($this->get_string_between($data, 'Place 3: ', ' (')) {
                            $player3 = $this->get_string_between($data, 'Place 3: ', ' (');
                            //dump('player3:'.$player3);
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


                        if ($this->get_string_between($data, 'FL', 'P')) {

                            $flop = $this->get_string_between($data, '[', ']');
                            $cardsFlop = explode(" ", $flop);
                            if(count($cardsFlop) > 1){
                            foreach ($cardsFlop as $cardFlop) {
                                $card = str_split($cardFlop);
                                $cardClass = $this->cardsRepository->findOneBy(['value' => $card[0], 'color' => $card[1]]);
                                $cardClass->setFlop($cardClass->getFlop() + 1);
                                $cardsFlop[] = $cardClass;
                                $this->entityManager->persist($cardClass);
                            }
                            $hand->setFlop($cardsFlop);
                            }
                        }

                        if ($turn = $this->get_string_between($data, 'TO', 'NANT')) {
                            $turnCard = $this->get_string_between($data, '] [', ']');
                            $card = str_split($turnCard);
                            $cardClass = $this->cardsRepository->findOneBy(['value' => $card[0], 'color' => $card[1]]);
                            $cardClass->setTurn($cardClass->getTurn() + 1);
                            $this->entityManager->persist($cardClass);
                            $hand->setTurn($cardClass);
                        }

                        if ($river = $this->get_string_between($data, 'RE *** [', ']')) {
                            $riverCard = $this->get_string_between($data, '] [', ']');
                            $card = str_split($riverCard);
                            $cardClass = $this->cardsRepository->findOneBy(['value' => $card[0], 'color' => $card[1]]);
                            $cardClass->setRiver($cardClass->getRiver() + 1);
                            $this->entityManager->persist($cardClass);
                            $hand->setRiver($cardClass);
                        }

                        if ($this->get_string_between($data, 'a remport', 'lors du pot')) {
                            $handWinner = explode(" a rem",$data);
                            $hand->setWin(0);
                            switch ($handWinner[0]) {
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
                            if($hand){
                                $this->entityManager->persist($hand);
                                $this->entityManager->flush();
                            }
                        }

                        if ($this->get_string_between($data, 'remporte ', 'tournoi')) {
                            $this->entityManager->persist($hand);
                            $this->entityManager->flush();
                            unset($player1bis, $player2bis, $player3bis);
                        }



                        if ($this->get_string_between($data, 'est', 'ouch')) {
                            $player = $this->get_string_between($data, ': ', ' (');
                            if ($player !== 'psychoman59') {
                                $playerfold = $this->playersRepository->findOneBy(['Pseudo' => $player]);
                                if(!$playerfold){
                                    $position = substr($data,7,1);
                                    if($position < 4){
                                        $playerpos = 'player'.$position;
                                        $player = $$playerpos;
                                        $playerfold = $this->playersRepository->findOneBy(['Pseudo' => utf8_encode($player)]);
                                        $playerfold->setWinHand($playerfold->getWinHand() - 1);
                                    }
                                }else{
                                    $playerfold->setWinHand($playerfold->getWinHand() - 1);
                                }

                            } else {
                                $hand->setWin(0);
                                $this->entityManager->persist($hand);
                                $this->entityManager->flush();
                            }
                        }

                        if ($this->importService->get_string_between($data, 'a gagn', 'avec')) {
                            $value ='';
                            if(strpos($data,'hauteur')){
                                $value = 'hauteur';
                            }elseif(strpos($data,'double paire')){
                                $value = 'double paire';
                            }elseif (strpos($data,'paire')){
                                $value = 'paire';
                            }elseif (strpos($data,'brelan')){
                                $value = 'brelan';
                            }elseif(strpos($data,'suite')){
                                $value = 'suite';
                            }elseif(strpos($data,'couleur,')){
                                $value = 'couleur';
                            }elseif (strpos($data,'full')){
                                $value = 'full';
                            }elseif(strpos($data,'carr')){
                                $value = 'carré';
                            }
                            if($value !== ''){
                                $result = $this->resultRepository->findOneBy(['value'=>$value]);
                                if($this->getWinnerOrLooser($data)){
                                    $result->setWin($result->getWin()+1);
                                }else{
                                    $result->setLoose($result->getLoose()+1);
                                }
                                $this->entityManager->persist($result);
                                $this->entityManager->flush();
                            }

                        }
                    }
                }





            }
            $filesystem = new Filesystem();
            $path = $fileData->getPath() . '/';
            $pathArchives = $path . '/../archives/';
            $filename = $fileData->getFilename();
            $filesystem->rename($path . $filename, $pathArchives . $filename);
        }
    }

    private function get_string_between($string, $start, $end)
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

    private function getWinnerOrLooser(string $data)
    {
        if(strpos($data,'psychoman59')){
            return true;
        }else{
            return false;
        }

    }
}
