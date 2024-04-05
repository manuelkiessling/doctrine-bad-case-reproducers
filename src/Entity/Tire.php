<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table]
class Tire
{
    public function __construct(
        string $name,
        Car    $car
    )
    {
        $this->name = $name;
        $this->car = $car;
        $car->addTire($this);
    }

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(
        type   : Types::INTEGER,
        options: ['unsigned' => true]
    )]
    private ?int $id = null;

    #[ORM\Column(
        type    : Types::STRING,
        length  : 255,
        nullable: false
    )]
    private string $name;

    #[ORM\ManyToOne(
        targetEntity: Car::class,
        cascade     : ['persist'],
        inversedBy  : 'tires'
    )]
    #[ORM\JoinColumn(name: 'car_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Car $car;

    public function getCar(): Car
    {
        return $this->car;
    }
}
