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
    name       : 'app:reproduce:good-case',
    description: ''
)]
class GoodCaseCommand extends Command
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
        $car1 = new Car('Toyota');
        $fl = new Tire('front left');
        $car1->addTire($fl);

        $car2 = new Car('Porsche');
        $car2->addTire($fl);
        $fl->setCar($car1);

        $this->entityManager->persist($car1);
        $this->entityManager->persist($car2);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
