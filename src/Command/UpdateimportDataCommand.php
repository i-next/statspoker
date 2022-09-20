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

#[AsCommand(
name:
'app:import-data-up',
    description: 'Add a short description for your command',
)]
class UpdateimportDataCommand extends Command
{
    private $entityManager;

    private $resultRepository;

    private $importService;

    private $importTournoi;

    public function __construct(
        EntityManagerInterface $entityManager,
        ResultRepository $resultRepository,
        ImportService $importService,
        ImportTournoi $importTournoi)
    {
        $this->entityManager = $entityManager;
        $this->resultRepository = $resultRepository;
        $this->importService = $importService;
        $this->importTournoi = $importTournoi;
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);



        $io->success('Import tournois');
        $this->importTournoi();
        return Command::SUCCESS;
    }


}
