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

    public function getName(): string
    {
        return $this->name;
    }

    /** @var Collection<Tire> */
    #[ORM\OneToMany(
        targetEntity: Tire::class,
        mappedBy    : 'car',
        cascade     : ['persist', 'remove']
    )]
    private Collection $tires;

    /** @return Tire[] */
    public function getTires(): array
    {
        // We do not want to return the collection itself,
        // as that would allow to modify "our"" collection
        // from outside this class.
        return $this->tires->toArray();
    }

    public function addTire(Tire $tire): void
    {
        if (!$this->tires->contains($tire)) {
            $tire->setCar($this);
            $this->tires->add($tire);
        }
    }

    public function removeTire(Tire $tire): void
    {
        if (!$this->tires->contains($tire)) {
            throw new ValueError('Tire not found');
        }

        $tire->removeFromCar();

        if (!is_null($tire->getCar())) {
            throw new ValueError('Tire is still associated with car. Use Tire::moveToCar() or Tire::removeFromCar() instead.');
        }
        $this->tires->removeElement($tire);
    }
}
