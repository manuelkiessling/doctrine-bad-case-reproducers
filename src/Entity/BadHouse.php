<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table]
class BadHouse
{
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->badWindows = new ArrayCollection();
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

    /** @var Collection<BadWindow> */
    #[ORM\OneToMany(
        targetEntity: BadWindow::class,
        mappedBy    : 'badHouse',
        cascade     : ['persist']
    )]
    private Collection $badWindows;

    /** @return Collection<BadWindow> */
    public function getBadWindows(): Collection
    {
        return $this->badWindows;
    }
}
