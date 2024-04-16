<?php

namespace App\Command;

use App\Entity\BadHouse;
use App\Entity\BadWindow;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name       : 'app:reproduce:bad-case-2',
    description: ''
)]
class BadCase2Command extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($this->getName());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    protected function execute(
        InputInterface  $input,
        OutputInterface $output
    ): int {
        $this->entityManager->getConnection()->executeStatement(
            "INSERT INTO {$this->entityManager->getClassMetadata(BadHouse::class)->getTableName()} (name) VALUES (?)",
            ['My house']
        );

        // Get id of the inserted house
        $houseId = $this->entityManager->getConnection()->lastInsertId();

        // Insert one window for the house
        $this->entityManager->getConnection()->executeStatement(
            "INSERT INTO {$this->entityManager->getClassMetadata(BadWindow::class)->getTableName()} (name, bad_house_id) VALUES (?, ?)",
            ['kitchen', $houseId]
        );

        $windowId = $this->entityManager->getConnection()->lastInsertId();

        $window = $this->entityManager->find(BadWindow::class, $windowId);

        $house = $window->getBadHouse();

        #$this->entityManager->remove($house);
        #$this->entityManager->flush();

        return Command::SUCCESS;
    }
}
