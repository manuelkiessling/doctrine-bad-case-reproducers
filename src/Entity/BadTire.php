<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table]
class BadTire
{
    public function __construct(
        string $name
    )
    {
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
        targetEntity: BadCar::class,
        cascade     : ['persist'],
        inversedBy  : 'tires'
    )]
    #[ORM\JoinColumn(
        name                : 'bad_car_id',
        referencedColumnName: 'id',
        nullable            : false,
        onDelete            : 'CASCADE'
    )]
    private ?BadCar $badCar = null;

    public function setBadCar(?BadCar $targetCar): void
    {
        $this->badCar = $targetCar;
    }

    public function getBadCar(): ?BadCar
    {
        return $this->badCar;
    }
}
