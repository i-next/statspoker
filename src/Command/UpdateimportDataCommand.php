<?php

namespace App\Command;

use App\Entity\Tournoi;
use SplFileObject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use App\Repository\TournoiResultRepository;
use App\Repository\TournoiRepository;
use App\Service\ImportService;
use App\Service\ImportTournoi;

#[AsCommand(
name:
'app:import-data-up',
    description: 'Add a short description for your command',
)]
class UpdateimportDataCommand extends Command
{
    private $entityManager;

    private $tournoiResultRepository;

    private $tournoiRepository;

    private $importService;

    private $importTournoi;

    public function __construct(
        EntityManagerInterface $entityManager,
        TournoiResultRepository $tournoiResultRepository,
        TournoiRepository $tournoiRepository,
        ImportService $importService,
        ImportTournoi $importTournoi)
    {
        $this->entityManager = $entityManager;
        $this->tournoiResultRepository = $tournoiResultRepository;
        $this->importService = $importService;
        $this->importTournoi = $importTournoi;
        $this->tournoiRepository = $tournoiRepository;
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);



        $io->success('Import tournois ');
        $this->importTournoi();
        return Command::SUCCESS;
    }
    private function importTournoi(): void
    {
        $tournois = $this->tournoiRepository->tournoiTicket();
        foreach ($tournois as $tournoi){
            $finder = new Finder();
            $finder->files()->in('datasrc/archives/tournois')->name('*'.$tournoi->getIdentifiant().'*.txt');
            foreach($finder as $file){
                $fileData = new SplFileObject($file);
                $this->importTournoi->updateTournoiTicket($fileData,$tournoi);
                dump($fileData->getFilename());
            }

        }
        die;
        /*$finder = new Finder();
        $finder->files()->in('datasrc/psychoman59')->depth(0);
        //dump($finder->count());die;
        foreach ($finder as $file) {
            $fileData = new SplFileObject($file);
            if(str_contains($fileData->getFilename(),'TS')){
                dump('ok'.$fileData->getFilename());
                $tournoi = $this->importTournoi->addTournoi($fileData);
                $tournoiResult = $this->importTournoi->importResult($tournoi);
                $filesystem = new Filesystem();
                $path = $fileData->getPath() . '/';
                $pathArchives = $path . '/../archives/tournois/';
                $filesystem->rename($path . $fileData->getFilename(), $pathArchives . $fileData->getFilename());
            }
        }*/
    }

}
