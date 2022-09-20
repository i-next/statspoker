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
use App\Repository\ResultRepository;
use App\Service\ImportService;
use App\Service\ImportTournoi;
use App\Service\ImportMain;

#[AsCommand(
name:
'app:import-data-new',
    description: 'Add a short description for your command',
)]
class ReimportDataCommand extends Command
{
    private $entityManager;

    private $resultRepository;

    private $importService;

    private $importTournoi;

    private $importMain;

    public function __construct(
        EntityManagerInterface $entityManager,
        ResultRepository $resultRepository,
        ImportService $importService,
        ImportTournoi $importTournoi,
        ImportMain $importMain)
    {
        $this->entityManager    = $entityManager;
        $this->resultRepository = $resultRepository;
        $this->importService    = $importService;
        $this->importTournoi    = $importTournoi;
        $this->importMain       = $importMain;
        parent::__construct();
    }

    protected function configure(): void
    {
//UPDATE `cards` SET `mycard`=0,`flopcard`=0,`turncard`=0,`rivercars`=0 WHERE 1;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);



        $io->success('Import tournois');
        $this->importTournoi();
        $io->success('Import Mains');
        //$this->importMains();
        return Command::SUCCESS;
    }

    private function importTournoi(): void
    {
        $finder = new Finder();
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
        }
    }

    private function importMains(): void
    {
        $finder = new Finder();
        $finder->files()->in('datasrc/psychoman59')->depth(0);
        foreach ($finder as $file) {
            $fileData = new SplFileObject($file);
            if (strtok($fileData->getFilename(), 'HH')) {
                dump($fileData->getFilename());
                $this->importMain->importMains($fileData);
                $filesystem = new Filesystem();
                $path = $fileData->getPath() . '/';
                $pathArchives = $path . '/../archives/hands/';
                // $filename = str_replace('archives','',$fileData->getFilename());
                $filesystem->rename($path . $fileData->getFilename(), $pathArchives . $fileData->getFilename());
            }
        }
    }
}
