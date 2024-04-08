<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table]
class Tire
{
    public function __construct(
        string $name
    ) {
        $this->name = $name;
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

    public function getName(): string
    {
        return $this->name;
    }

    #[ORM\ManyToOne(
        targetEntity: Car::class,
        inversedBy  : 'tires'
    )]
    #[ORM\JoinColumn(
        name                : 'car_id',
        referencedColumnName: 'id',
        nullable            : false,
        onDelete            : 'CASCADE'
    )]
    private ?Car $car = null;

    public function setCar(?Car $targetCar): void
    {
        // By not exposing self::$car directly, we can add logic here to
        // ensure that the Tire is removed from the previous Car (if any) before
        // being added to the new one.
        // This way, the system can never end up in a state where a Tire has
        // assigned Car entity B on the Tire entity, while there is still a reference to
        // this Tire entity on the tires collection of Car entity B.

        if ($this->car === $targetCar) {
            return;
        }

        $this->removeFromCar();

        if (is_null($targetCar)) {
            return;
        }

        $this->car = $targetCar;
        $targetCar->addTire($this);
    }

    public function removeFromCar(): void
    {
        // Same as above — we do not expose self::$car directly, so we can
        // add logic here to ensure that the Tire is removed from the Car
        // before the Car is being removed from the Tire.

        if (is_null($this->car)) {
            return;
        }

        $currentCar = $this->car;

        $this->car = null;
        $currentCar->removeTire($this);
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }
}
