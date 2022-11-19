<?php


namespace App\Service;

use SplFileObject;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tournoi;
use App\Entity\TournoiResult;
use App\Repository\TournoiRepository;
use App\Repository\TournoiResultRepository;
use App\Service\DataService;

class ImportTournoi
{

    private $entityManager;
    private $tournoiRepository;
    private $tournoiResultRepository;
    private $dataService;

    public function __construct(
        EntityManagerInterface $entityManager,
        TournoiRepository $tournoiRepository,
        TournoiResultRepository $tournoiResultRepository,
        DataService $dataService
    )
    {
        $this->entityManager = $entityManager;
        $this->tournoiRepository = $tournoiRepository;
        $this->tournoiResultRepository = $tournoiResultRepository;
        $this->dataService = $dataService;
    }


    /**
     * @param SplFileObject $fileData
     * @return Tournoi
     */
    public function addTournoi(SplFileObject $fileData): Tournoi
    {

        while (!$fileData->eof()) {
            $data = $fileData->fgets();
            //dump($data,str_contains($data,'Buy-in'));
            if (str_contains($data, 'PokerStars Tournoi')) {
                $idTour = $this->dataService->get_string_between($data, '#', ',');
                $tournoi = $this->tournoiRepository->findOneBy(['identifiant' => $idTour]) ?: new Tournoi();
                $tournoi->setIdentifiant((int)$idTour);
            }
            if (str_contains($data, 'Freeroll')) {
                $tournoi->setBuyin(0);
            }
            if (str_contains($data, 'Buy-in') && !str_contains($data, 'cible')) {
                $buyin1 = $this->dataService->get_string_between($data, '€', '/');
                $buyin2 = $this->dataService->get_string_between($data, '/€', ' EUR');
                $buyin = (float)$buyin1 + (float)$buyin2;
                //$buyin = floatval((float)$buyin1+$buyin2);
                dump($buyin);
                $tournoi->setBuyin($buyin);
            }
            if (str_contains($data, 'joueurs')) {
                $tournoi->setNbplayer(strtok($data, ' joueurs'));
            }
            if (str_contains($data, 'Dotation')) {
                $prizepool = $this->dataService->get_string_between($data, '€', ' EUR');
                $tournoi->setPrizepool($prizepool);
            }
            if (str_contains($data, 'Vous avez')) {
                $position = $this->dataService->get_string_between($data, ' la ', ' place');
                switch ($position) {
                    case '1er':
                        $tournoi->setWin(1);
                        $tournoi->setPosition(1);
                        break;
                    case '2e' :
                        $tournoi->setWin(0);
                        $tournoi->setPosition(2);
                        break;
                    case '3e' :
                        $tournoi->setWin(0);
                        $tournoi->setPosition(3);
                        break;
                    default:
                        $tournoi->setWin(0);
                        $tournoi->setPosition($this->dataService->get_string_between($data, ' la ', 'e place'));
                }
            }
            if (str_contains($data, 'psychoman59')) {
                if ($money = str_replace(',', '.', $this->dataService->get_string_between($data, '€', ' ('))) {
                    $tournoi->setMoney((float)$money-$tournoi->getBuyin());
                    $tournoi->setTicket(false);
                } elseif(str_contains($data, 'Ticket')) {
                    $tournoi->setTicket(true);
                    $money = str_replace(',', '.', $this->dataService->get_string_between($data, '€', ')'));
                    if(strlen($money) > 10){
                        $money = str_replace(',', '.', $this->dataService->get_string_between($data, '(€', ' '));
                    }
                    $tournoi->setMoney($money);
                }else{
                    $tournoi->setMoney(-$tournoi->getBuyin());
                    $tournoi->setTicket(false);
                }
            }
        }

        if (!$tournoi->getPrizepool()) {
            $tournoi->setPrizepool(0);
        }

        $dateString = $this->dataService->get_string_between($fileData->getFilename(), 'TS', ' T');
        $date = \DateTime::createFromFormat('Ymd', $dateString);
        $tournoi->setDate($date);
        $this->entityManager->persist($tournoi);
        $this->entityManager->flush();
        return $tournoi;
    }

    public function importResult(Tournoi $tournoi): bool
    {
        $result = false;

        $tournoiResult = $this->tournoiResultRepository->findOneBy(['buyin' => $tournoi->getBuyin(), 'prizepool' => $tournoi->getPrizepool()]);
        if (!$tournoiResult) {
            $tournoiResult = new TournoiResult();
            $tournoiResult->setIdentifiant((string)$tournoi->getBuyin() . '/' . $tournoi->getPrizepool());
            $tournoiResult->setBuyin($tournoi->getBuyin());
            $tournoiResult->setPrizepool($tournoi->getPrizepool());
            $tournoiResult->setWin(0);
            $tournoiResult->setNbtour(0);
        }

        $win = (int)$tournoi->getWin() ? 1 : 0;
        $tournoiResult->setNbtour($tournoiResult->getNbtour() + 1);
        $tournoiResult->setWin($tournoiResult->getWin() + $win);
        $tournoiResult->setMoney($tournoiResult->getMoney() + $tournoi->getMoney());
        $tournoiResult->setTicket($tournoi->getTicket());
        $result = true;
        $this->entityManager->persist($tournoiResult);
        $this->entityManager->flush();
        return $result;
    }

    public function updateTournoiTicket(SplFileObject $fileData, Tournoi $tournoi)
    {
        $money = 0;
        while (!$fileData->eof()) {
            $data = $fileData->fgets();

            if (str_contains($data, 'Tournoi cible')) {
                $money = $this->dataService->get_string_between($data, '€', ' EUR');
            }
            if(str_contains($data, 'psychoman59') && str_contains($data,'qualifiés')){
                $tournoiResult = $this->tournoiResultRepository->findOneBy(['buyin' => $tournoi->getBuyin(),'prizepool' => $tournoi->getPrizepool()]);
                $tournoi->setWin(true);
                $tournoi->setMoney($money);
                $tournoiResult->setWin($tournoiResult->getWin()+1);
                $tournoiResult->setMoney($tournoiResult->getMoney()+$money+$tournoi->getBuyin());
                $this->entityManager->persist($tournoiResult);
                $this->entityManager->persist($tournoi);
                $this->entityManager->flush();
            }
        }
    }

    public function updateDateTournoi(string $filename): bool
    {
        $dataTournoi = explode(' ', $filename);
        $idTournoi = substr($dataTournoi[1], 1);
        //$idTournoi = $this->dataService->get_string_between($filename, 'T', ' Hold');
        $tournoi = $this->tournoiRepository->findOneBy(['identifiant' => $idTournoi]);

        if ($tournoi instanceof Tournoi) {
            $dateString = $this->dataService->get_string_between($filename, 'TS', ' T');
            $date = \DateTime::createFromFormat('Ymd', $dateString);
            $tournoi->setDate($date);
            $this->entityManager->persist($tournoi);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }
}