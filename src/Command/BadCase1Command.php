<?php

namespace App\Command;

use App\Entity\BadCar;
use App\Entity\BadTire;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name       : 'app:reproduce:bad-case-1',
    description: ''
)]
class BadCase1Command extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($this->getName());
    }

    /**
     * @throws Exception
     */
    protected function execute(
        InputInterface  $input,
        OutputInterface $output
    ): int {

        // Delete all cars
        $this->entityManager->getConnection()->executeStatement(
            "DELETE FROM {$this->entityManager->getClassMetadata(BadCar::class)->getTableName()};"
        );

        for ($i = 0; $i < 2; $i++) {
            $badCar = new BadCar("Toyota $i");

            $this->entityManager->persist($badCar);
            $this->entityManager->flush();

            $badTire = new BadTire("front left $i");

            $badTire->setBadCar($badCar);

            $this->entityManager->persist($badTire);
            $this->entityManager->flush();

            $this->entityManager->remove($badCar);
            $this->entityManager->flush();
        }

        // At this point, 'Toyota 0' is still in the database,
        // although the entity for this database entry went
        // through $em::remove() and $em::flush() above.

        $cars = $this->entityManager->getConnection()->executeQuery(
            "SELECT * FROM {$this->entityManager->getClassMetadata(BadCar::class)->getTableName()};"
        )->fetchAllAssociative();

        foreach ($cars as $car) {
            $output->writeln($car['name']);
        }

        // The reason for this is as follows:
        // On lines 45-50, we create a new BadTire entity,
        // and set its $badCar property to the BadCar entity
        // we created and persisted before.
        //
        // At this point, both entities are managed by the
        // EntityManager, and the BadTire entity is associated
        // with the BadCar entity.
        //
        // However, the $badTires collection property of the
        // BadCar entity does NOT contain the BadTire entity.
        //
        // As a result, removing the BadCar entity does not
        // cascade to the BadTire entity on the Entity Manager
        // level.
        //
        // (It does, however, cascade on the database level,
        // because the foreign key constraint is set to
        // ON DELETE CASCADE. Thus, the database entry for the
        // BadTire entity is removed when the BadCar entity is
        // removed).
        //
        // While we did declare cascade: ['persist', 'remove']
        // on the $badTires property of the BadCar entity, this
        // doesn't have an effect in this case, simply because there
        // isn't a BadTire entity in the $badTires collection property
        // to which the removal could be cascaded.
        //
        // Then the next foreach iteration starts, and we create
        // and persist another BadCar entity ("Toyota 1").
        //
        // This is where the issue arises:
        //
        // During the $em::persist for the new BadCar entity,
        // The Entity Manager realizes that it still manages
        // a BadTire entity — the one we created and persisted
        // in the first iteration, but never removed through
        // the Entity Manager as explained above.
        //
        // While the Entity Manager realizes that it doesn't need
        // to sync the BadTire entity with the database (because it
        // already persisted it in the first iteration, and it didn't
        // change since then), it does realize that the BadTire entity
        // is still associated with a BadCar entity on its $badCar property.
        //
        // This is, of course, the BadCar entity from the first iteration,
        // which we removed through the Entity Manager in the first iteration.
        //
        // When the BadCar removal on the first iteration occurred, the Entity
        // Manager "forgot" about this entity PHP object — thus, the entity object
        // it finds on the $badCar property of the BadTire entity is a completely
        // new BadCar instance from the Entity Manager's perspective, and one
        // one that it never synced with the database.
        //
        // No problem from the Entity Manager's perspective, though, because
        // the ManyToOne relationship on the BadTire::$badCar property is set
        // to cascade: ['persist'] (see file BadTire.php, line 41).
        //
        // The Entity manager will therefore happily obey and persist the new
        // BadCar entity.
        //
        // As a result, our "Toyota 0" database entry returns from the dead,
        // and at the end of our command run, we have a row on table bad_car
        // with the name "Toyota 0", even though we explicitly removed it
        // through the Entity Manager on the first iteration.
        //
        // This behavior can be easily proved by removing the cascade: ['persist']
        // attribute from the BadTire::$badCar property (line 41 in BadTire.php).
        //
        // When you do this, running the command will throw an exception:
        //
        // A new entity was found through the relationship 'App\Entity\BadTire#badCar'
        // that was not configured to cascade persist operations for entity:
        // App\Entity\BadCar...
        //
        // Here is what happens on the SQL level when running this command:
        //
        // first iteration:
        // INSERT INTO bad_car ...  // Toyota 0
        // INSERT INTO bad_tire ... // front left 0
        // DELETE FROM bad_car ...  // Toyota 0
        //
        // second iteration:
        // INSERT INTO bad_car ...  // Toyota 1
        // INSERT INTO bad_car ...  // Toyota 0      <-- this is the issue
        // INSERT INTO bad_tire ... // front left 1
        // DELETE FROM bad_car ...  // Toyota 1
        //
        // At this point, the database contains a row for "Toyota 0",
        // even though we explicitly removed it through the Entity Manager
        // in the first iteration.

        return Command::SUCCESS;
    }
}
