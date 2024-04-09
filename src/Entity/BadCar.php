<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table]
class BadCar
{
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->badTires = new ArrayCollection();
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

    /** @var Collection<BadTire> */
    #[ORM\OneToMany(
        targetEntity: BadTire::class,
        mappedBy    : 'badCar',
        cascade     : ['persist', 'remove']
    )]
    private Collection $badTires;

    /** @return Collection<BadTire> */
    public function getBadTires(): Collection
    {
        return $this->badTires;
    }
}
