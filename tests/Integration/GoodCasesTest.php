<?php

namespace App\Tests\Integration;

use App\Entity\Car;
use App\Entity\Tire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GoodCasesTest extends KernelTestCase
{
    public function testGoodCases(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $em = $container->get(
            EntityManagerInterface::class
        )->getManager();

        $car1 = new Car('Toyota');
        $fl = new Tire('front left');
        $car1->addTire($fl);

        $car2 = new Car('Porsche');
        $car2->addTire($fl);
        $fl->setCar($car1);

        $em->persist($car1);
        $em->persist($car2);
        $em->flush();

        $sql = "SELECT id FROM car WHERE name = 'Toyota'";
        $stmt = $em->getConnection()->prepare($sql);
        $result = $stmt->executeQuery();

        $sql = "SELECT car_id FROM tire WHERE name = 'front left'";
        $stmt = $em->getConnection()->prepare($sql);
        $result = $stmt->executeQuery();

        // Assert that the tire is associated with the first car
        $this->assertSame(
            $car1->getId(),
            $result->fetchOne()
        );
    }
}
