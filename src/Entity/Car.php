<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ValueError;


#[ORM\Entity]
#[ORM\Table]
class Car
{
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->tires = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(
        type   : Types::INTEGER,
        options: ['unsigned' => true]
    )]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(
        type    : Types::STRING,
        length  : 255,
        nullable: false
    )]
    private string $name;

    #[ORM\OneToMany(
        targetEntity: Tire::class,
        mappedBy    : 'car',
        cascade     : ['persist', 'remove']
    )]
    private Collection $tires;

    public function getTires(): Collection
    {
        return $this->tires;
    }

    public function addTire(Tire $tire): void
    {
        if (!$this->tires->contains($tire)) {
            if ($tire->getCar() !== $this) {
                throw new ValueError('Tire does not belong to this car');
            }
            $this->tires->add($tire);
        }
    }
}
