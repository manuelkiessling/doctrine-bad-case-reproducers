<?php

namespace App\Command;

use App\Entity\Car;
use App\Entity\Tire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name       : 'app:reproduce',
    description: 'Reproduce the issue'
)]
class ReproduceCommand extends Command
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
        ini_set('memory_limit', '2G');

        for ($i = 0; $i < 2; $i++) {
            $car = new Car('Toyota');
            $this->entityManager->persist($car);

            $tire = new Tire('front left', $car);

            #$car->addTire($tire);

            $this->entityManager->persist($tire);

            $this->entityManager->flush();

            #$this->entityManager->refresh($car);

            $this->entityManager->remove($car);

            $this->entityManager->flush();

            $output->write('.');
        }

        $output->writeln('');

        return Command::SUCCESS;
    }
}
