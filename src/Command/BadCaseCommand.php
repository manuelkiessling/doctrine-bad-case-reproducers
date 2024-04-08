<?php

namespace App\Command;

use App\Entity\BadCar;
use App\Entity\BadTire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name       : 'app:reproduce:bad-case',
    description: 'Reproduce the issue'
)]
class BadCaseCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($this->getName());
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output
    ): int {
        for ($i = 0; $i < 2; $i++) {
            $badCar = new BadCar("Toyota $i");
            $this->entityManager->persist($badCar);

            $badTire = new BadTire("front left $i");
            $badTire->setBadCar($badCar);

            $this->entityManager->persist($badTire);

            $this->entityManager->flush();

            $this->entityManager->refresh($badCar);

            $this->entityManager->remove($badCar);

            $this->entityManager->flush();

            $output->write('.');
        }

        // At this point, 'Toyota 0' is still in the database.

        $output->writeln('');

        return Command::SUCCESS;
    }
}
