<?php


namespace App\Service;

use App\Repository\MesMainsRepository;
use SplFileObject;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Main;
use App\Entity\MesMains;
use App\Entity\Joueur;
use App\Repository\MainRepository;
use App\Repository\MesMainsRepositoryRepository;
use App\Repository\TournoiRepository;
use App\Repository\JoueurRepository;
use App\Repository\CardsRepository;
use App\Service\DataService;
use function Symfony\Component\Finder\contains;

class ImportMain
{

    private $entityManager;
    private $mainRepository;
    private $mesMainsRepository;
    private $joueursRepository;
    private $dataService;
    private $tournoiRepository;
    private $cardsRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MainRepository $mainRepository,
        MesMainsRepository $mesMainsRepository,
        TournoiRepository $tournoiRepository,
        JoueurRepository $joueursRepository,
        CardsRepository $cardsRepository,
        DataService $dataService
    ){
        $this->entityManager            = $entityManager;
        $this->mainRepository           = $mainRepository;
        $this->mesMainsRepository       = $mesMainsRepository;
        $this->tournoiRepository        = $tournoiRepository;
        $this->joueursRepository        = $joueursRepository;
        $this->cardsRepository          = $cardsRepository;
        $this->dataService              = $dataService;
    }

    public function importMains(SplFileObject $fileData): void
    {
        $arrayFilename = explode(' ',$fileData->getFilename());

        //$tournoiId = substr($arrayFilename[1],1);
        $tournoiId = $arrayFilename[1];
        $tournoi = $this->tournoiRepository->findOneBy(['identifiant'=>(int)$tournoiId]);
        if(is_null($tournoi)){
            $tournoiId = substr($arrayFilename[1],1);
            $tournoi = $this->tournoiRepository->findOneBy(['identifiant'=>(int)$tournoiId]);
        };
        if(!is_null($tournoi)){
        while (!$fileData->eof()) {
            $data = $fileData->fgets();
            if(str_contains($data,'Main PokerStar')){

                $main = new Main();
                $main->setIdTournoi($tournoi);
                dump($tournoi->getId());
            }
            if(str_contains($data,'Place 1')){
                $pseudo1 = $this->dataService->get_string_between($data, '1: ', ' (');

                if($pseudo1 !== 'psychoman59'){
                    $player1 = $this->joueursRepository->findOneBy(['pseudo' => $this->dataService->convertPseudo($pseudo1)]);
                    if(!$player1){
                        $player1 = new Joueur();
                    }
                    $name[$this->dataService->convertPseudo($pseudo1)] = $player1;
                    $player1->setPseudo($this->dataService->convertPseudo($pseudo1));
                    $main->setIdPlayer1($player1);
                }
            }
            if(str_contains($data,'Place 2')){

                $pseudo2 = $this->dataService->get_string_between($data, '2: ', ' (');
                if($pseudo2 !== 'psychoman59'){
                    $player2 = $this->joueursRepository->findOneBy(['pseudo' => $this->dataService->convertPseudo($pseudo2)]);
                    if(!$player2){
                        $player2 = new Joueur();
                    }
                    $name[$this->dataService->convertPseudo($pseudo2)] = $player2;
                    $player2->setPseudo($this->dataService->convertPseudo($pseudo2));
                    $main->setIdPlayer2($player2);
                }
            }
            if(str_contains($data,'Place 3')){
                $pseudo3 = $this->dataService->get_string_between($data, '3: ', ' (');
                if($pseudo3 !== 'psychoman59'){
                    $player3 = $this->joueursRepository->findOneBy(['pseudo' => $this->dataService->convertPseudo($pseudo3)]);
                    if(!$player3){
                        $player3 = new Joueur();
                    }
                    $name[$this->dataService->convertPseudo($pseudo3)] = $player3;
                    $player3->setPseudo($this->dataService->convertPseudo($pseudo3));
                    $main->setIdPlayer3($player3);
                }
            }
            if(str_contains($data,'Place 4')){
                $pseudo4 = $this->dataService->get_string_between($data, '4: ', ' (');
                if($pseudo4 !== 'psychoman59'){
                    $player4 = $this->joueursRepository->findOneBy(['pseudo' => $this->dataService->convertPseudo($pseudo4)]);
                    if(!$player4){
                        $player4 = new Joueur();
                    }
                    $name[$this->dataService->convertPseudo($pseudo4)] = $player4;
                    $player4->setPseudo($this->dataService->convertPseudo($pseudo4));
                    $main->setIdPlayer4($player4);
                }
            }
            if(str_contains($data,'Place 5')){
                $pseudo5 = $this->dataService->get_string_between($data, '5: ', ' (');
                if($pseudo5 !== 'psychoman59'){
                    $player5 = $this->joueursRepository->findOneBy(['pseudo' => $this->dataService->convertPseudo($pseudo5)]);
                    if(!$player5){
                        $player5 = new Joueur();
                    }
                    $name[utf8_encode($this->dataService->convertPseudo($pseudo5))] = $player5;
                    $player5->setPseudo($this->dataService->convertPseudo($pseudo5));
                    $main->setIdPlayer5($player5);
                }
            }
            if(str_contains($data,'Place 6')){
                $pseudo6 = $this->dataService->get_string_between($data, '6: ', ' (');
                if($pseudo6 !== 'psychoman59'){
                    $player6 = $this->joueursRepository->findOneBy(['pseudo' => $this->dataService->convertPseudo($pseudo6)]);
                    if(!$player6){
                        $player6 = new Joueur();
                    }
                    $name[$this->dataService->convertPseudo($pseudo6)] = $player6;
                    $player6->setPseudo($this->dataService->convertPseudo($pseudo6));
                    $main->setIdPlayer6($player6);
                }
            }
            if(str_contains($data,'Place 7')){
                $pseudo7 = $this->dataService->get_string_between($data, '7: ', ' (');
                if($pseudo7 !== 'psychoman59'){
                    $player7 = $this->joueursRepository->findOneBy(['pseudo' => $this->dataService->convertPseudo($pseudo7)]);
                    if(!$player7){
                        $player7 = new Joueur();
                    }
                    $name[$this->dataService->convertPseudo($pseudo7)] = $player7;
                    $player7->setPseudo($this->dataService->convertPseudo($pseudo7));
                    $main->setIdPlayer7($player7);
                }
            }
            if(str_contains($data,'Place 8')){
                $pseudo8 = $this->dataService->get_string_between($data, '8: ', ' (');
                if($pseudo8 !== 'psychoman59'){
                    $player8 = $this->joueursRepository->findOneBy(['pseudo' => $this->dataService->convertPseudo($pseudo8)]);
                    if(!$player8){
                        $player8 = new Joueur();
                    }
                    $name[$this->dataService->convertPseudo($pseudo8)] = $player8;
                    $player8->setPseudo($this->dataService->convertPseudo($pseudo8));
                    $main->setIdPlayer8($player8);
                }
            }
            if(str_contains($data,'Place 9')){
                $pseudo9 = $this->dataService->get_string_between($data, '9: ', ' (');
                if($pseudo9 !== 'psychoman59'){
                    $player9 = $this->joueursRepository->findOneBy(['pseudo' => $this->dataService->convertPseudo($pseudo9)]);
                    if(!$player9){
                        $player9 = new Joueur();
                    }
                    $name[$this->dataService->convertPseudo($pseudo9)] = $player9;
                    $player9->setPseudo($this->dataService->convertPseudo($pseudo9));
                    $main->setIdPlayer9($player9);
                }
            }
            if(str_contains($data,'Distri')){
                $hand = $this->dataService->get_string_between($data, '[', ']');
                $cards = explode(' ',$hand);
                $card1 = $this->cardsRepository->findOneBy(['value' => substr($cards[0],0,1), 'color' => substr($cards[0],1)]);
                $card1->setMycard($card1->getMycard()+1);
                $card2 = $this->cardsRepository->findOneBy(['value' => substr($cards[1],0,1), 'color' => substr($cards[1],1)]);
                $card2->setMycard($card2->getMycard()+1);

                $main->setIdCard1($card1);
                $main->setIdCard2($card2);

                $this->entityManager->persist($card1,$card2);
                $this->entityManager->flush();


                if($card1->getId()>$card2->getId()){
                    $myhand = $this->mesMainsRepository->findOneBy(['id_card1'=>$card1,'id_card2'=>$card2]);
                    if(!$myhand){
                        $myhand = new MesMains();
                        $myhand->setIdCard1($card1);
                        $myhand->setIdCard2($card2);
                    }
                }else{
                    $myhand = $this->mesMainsRepository->findOneBy(['id_card1'=>$card2,'id_card2'=>$card1]);
                    if(!$myhand){
                        $myhand = new MesMains();
                        $myhand->setIdCard1($card2);
                        $myhand->setIdCard2($card1);
                    }

                }
                if(!isset($myhand)){
                    dump($card1->getId(),$card2->getId());die;
                }
                $myhand->setCount($myhand->getCount()+1);

            }
            if(str_contains($data,'passe')){
                $namePseudo = $this->dataService->getPseudoFirstPosition($data);

                if($namePseudo === 'psychoman59'){
                    $myhand->setWin($myhand->getWin()-1);
                }else{
                    //dump($namePseudo);die;
                        if(!isset($name[$namePseudo])){
                            $namePseudoArray = explode(' ',trim($data));
                            $namePseudo = $this->dataService->convertPseudo($namePseudoArray[0]);
                            $i = 1;
                            while(!isset($name[$namePseudo])){

                                $newName = substr($namePseudo,0,-3);
                                $namePseudo = $namePseudo. " " . $this->dataService->convertPseudo($namePseudoArray[$i]);
                                if(isset($name[$newName])){
                                    $namePseudo = $newName;
                                    break;
                                }elseif(isset($name[utf8_encode($newName)])){
                                    $namePseudo = utf8_encode($newName);
                                    break;
                                }
                                $i++;
                            }
                        }

                    $looser = $name[$namePseudo];
                    $looser->setHandWin($looser->getHandWin()-1);
                }
            }
            if(str_contains($data,'a remport')){
                $dataPseudoWinner = explode(' ',trim($data));
                $pseudoWinner = $this->dataService->convertPseudo($dataPseudoWinner[0]);
                if($pseudoWinner === 'psychoman59'){
                    $myhand->setWin($myhand->getWin()+1);
                    $main->setWin(true);
                    unset($winner);
                }elseif (!str_contains($pseudoWinner,'Siege') && !str_contains($pseudoWinner,'Siège')){
                    $i = 1;
                    $main->setWin(false);
                    while(!isset($name[$pseudoWinner] )){
                        $pseudoWinner = $pseudoWinner. " " . $this->dataService->convertPseudo($dataPseudoWinner[$i]);
                        $newNameWinner = substr($pseudoWinner,0,-3);
                        if(isset($name[utf8_encode($pseudoWinner)])){
                            $pseudoWinner = utf8_encode($pseudoWinner);
                            break;
                        }elseif(isset($name[$newNameWinner])){
                            $pseudoWinner = $newNameWinner;
                            break;
                        }elseif(isset($name[utf8_encode($newNameWinner)])){
                            $pseudoWinner = utf8_encode($newNameWinner);
                            break;
                        }
                        $i++;
                    }
                    $winner = $name[$pseudoWinner];
                    $winner->setHandWin($winner->getHandWin()+1);
                    $this->entityManager->persist($winner);
                    $this->entityManager->flush();
                }
            }
            if(str_contains($data,'FLOP')){
                $flop = $this->dataService->get_string_between($data, '[', ']');
                $flopCards = explode(' ',$flop);
                $flopCard1 = $this->cardsRepository->findOneBy(['value' => substr($flopCards[0],0,1), 'color' => substr($flopCards[0],1)]);
                $flopCard1->setFlopcard($flopCard1->getFlopcard()+1);
                $flopCard2 = $this->cardsRepository->findOneBy(['value' => substr($flopCards[1],0,1), 'color' => substr($flopCards[1],1)]);
                $flopCard2->setFlopcard($flopCard2->getFlopcard()+1);
                $flopCard3 = $this->cardsRepository->findOneBy(['value' => substr($flopCards[2],0,1), 'color' => substr($flopCards[2],1)]);
                $flopCard3->setFlopcard($flopCard3->getFlopcard()+1);
                $main->setIdFlop1($flopCard1);
                $main->setIdFlop2($flopCard2);
                $main->setIdFlop3($flopCard3);

            }
            if(str_contains($data,'TOURNANT')) {
                $turnFull = substr(trim($data), -4);
                $turn = $this->dataService->get_string_between($turnFull, '[', ']');
                if($turn !== ""){
                    $turnCards = explode(' ', $turn);
                    $turnCard = $this->cardsRepository->findOneBy(['value' => substr($turnCards[0], 0, 1), 'color' => substr($turnCards[0], 1)]);
                    $turnCard->setTurncard($turnCard->getTurncard() + 1);
                    $main->setIdTurn($turnCard);
                }

            }
            if(str_contains($data,'RIVI')) {
                $riv = substr(trim($data), -4);
                $river = $this->dataService->get_string_between($riv, '[', ']');
                $riverCards = explode(' ', $river);
                $riverCard = $this->cardsRepository->findOneBy(['value' => substr($riverCards[0], 0, 1), 'color' => substr($riverCards[0], 1)]);
                $riverCard->setRivercars($riverCard->getRivercars() + 1);
                $main->setIdRiver($riverCard);

            }
            if(str_contains($data,'SYNTH')){
                foreach($name as $player){
                    $this->entityManager->persist($player);
                }
                $this->entityManager->persist($myhand);
                $this->entityManager->persist($main);
                if(isset($riverCard)){
                    $this->entityManager->persist($riverCard);
                }
                if(isset($turnCard)){
                    $this->entityManager->persist($turnCard);
                }
                if(isset($flopCard1)){
                    $this->entityManager->persist($flopCard1);
                }
                if(isset($flopCard2)){
                    $this->entityManager->persist($flopCard2);
                }
                if(isset($flopCard3)){
                    $this->entityManager->persist($flopCard3);
                }
                $this->entityManager->flush();
            }

        }
        if(isset($winner)){
            $winner->setTourwin($winner->getTourwin()+1);
            foreach($name as $play){
                if($play !== $winner){
                    $play->setTourwin($play->getTourwin()-1);
                }
            }
            $this->entityManager->persist($winner);
        }

        $this->entityManager->flush();


    }
    }


}