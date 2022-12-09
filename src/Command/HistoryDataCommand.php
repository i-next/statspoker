<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use SplFileObject;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tournoi;
use App\Entity\TournoiResult;
use App\Entity\Paris;
use App\Repository\TournoiResultRepository;
use App\Repository\TournoiRepository;


#[AsCommand(
    name: 'app:history:data',
    description: 'Add a short description for your command',
)]
class HistoryDataCommand extends Command
{
    private const INDEXTOURNOI =[0,2,3,6,9];

    private $entityManager;

    private $tournoiRepository;

    private $tournoiResultRepository;

    public function __construct(
        EntityManagerInterface  $entityManager,
        TournoiRepository       $tournoiRepository,
        TournoiResultRepository $tournoiResultRepository
    ){
        $this->entityManager            = $entityManager;
        $this->tournoiRepository        = $tournoiRepository;
        $this->tournoiResultRepository  = $tournoiResultRepository;
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $io->success('Import Audit PokerStar');
        $this->importAudit();
        return Command::SUCCESS;
    }

    private function importAudit(): void
    {
        $finder = new Finder();
        $finder->files()->in('datasrc/psychoman59')->depth(0);
        foreach ($finder as $file) {
            $fileData = new SplFileObject($file);
            if(str_contains($fileData->getFilename(),'html')){
                $crawler = new Crawler(file_get_contents($fileData->getPathname()));
                $table = $crawler->filter('table')->filter('tr')->each(function ($tr, $i) {
                    return $tr->filter('td')->each(function ($td, $i) {
                        if(in_array($i,self::INDEXTOURNOI)){
                            return trim($td->text());
                        }
                    });
                });
                $allTournoi = [];
                foreach($table as $tournoi){
                    if(array_key_exists(0,$tournoi) && $tournoi[0] && $tournoi[6] !== "0,00Â "){
                        $dateArray = explode(' ',$tournoi[0]);
                        $date = \DateTime::createFromFormat('d/m/Y', $dateArray[0]);
                        $win = floatval(rtrim(preg_replace('~[(Â) ]~', '',str_replace(',','.',$tournoi[6]))));
                        if($tournoi[2][0] === "4"){
                            $pari = new Paris();
                            $pari->setWin($win);
                            $pari->setDate($date);
                            $this->entityManager->persist($pari);
                            $this->entityManager->flush();
                        }elseif(str_contains($tournoi[6],'(')){
                            $tournoiPs = new Tournoi();
                            $tournoiPs->setIdentifiant($tournoi[2]);
                            $tournoiPs->setDate($date);
                            $tournoiPs->setBuyin($win);
                            $tournoiPs->setPrizepool($win*3);
                            $tournoiPs->setNbplayer(3);
                            $tournoiPs->setWin(false);
                            $tournoiPs->setTicket(false);
                            $tournoiPs->setMoney(-$win);
                            $allTournoi[$tournoi[2]] = $tournoiPs;
                            $this->entityManager->persist($tournoiPs);
                            $this->entityManager->flush();
                        }else{
                            $tournoiPs = $this->tournoiRepository->findOneBy(['identifiant' => $tournoi[2]]);
                            $tournoiPs?:dump($tournoi);
                            $tournoiPs->setWin(true);
                            $tournoiPs->setPosition(1);
                            $tournoiPs->setMoney($win);
                            $tournoiPs->setPrizepool($win);
                            $allTournoi[$tournoi[2]] = $tournoiPs;
                            $this->entityManager->persist($tournoiPs);
                            $this->entityManager->flush();
                        }
                    }
                }
                foreach($allTournoi as $tournoi){
                    $tournoiResult = $this->tournoiResultRepository->findOneBy(['identifiant' => $tournoi->getBuyin().'/'.$tournoi->getPrizepool()]);
                    if($tournoiResult){
                        $tournoiResult->setMoney($tournoiResult->getMoney()+$tournoi->getMoney());
                        $tournoiResult->setNbtour($tournoiResult->getNbtour()+1);
                        if($tournoi->getWin()){
                            $tournoiResult->setWin($tournoiResult->getWin()+1);
                        }else{
                            $tournoiResult->setWin($tournoiResult->getWin()-1);
                        }
                    }else{
                        $tournoiResult = new TournoiResult();
                        $tournoiResult->setIdentifiant($tournoi->getBuyin().'/'.$tournoi->getPrizepool());
                        $tournoiResult->setNbtour(1);
                        $tournoiResult->setBuyin($tournoi->getBuyin());
                        $tournoiResult->setMoney($tournoi->getMoney());
                        $tournoiResult->setTicket(0);
                        $tournoiResult->setPrizepool($tournoi->getPrizepool());
                        if($tournoi->getWin()){
                            $tournoiResult->setWin(1);
                        }else{
                            $tournoiResult->setWin(-1);
                        }
                    }

                    $this->entityManager->persist($tournoiResult);
                    $this->entityManager->flush();
                }
            }
        }
    }
}
