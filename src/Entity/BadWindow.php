<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table]
class BadWindow
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
        targetEntity: BadHouse::class,
        cascade     : ['persist'],
        inversedBy  : 'windows'
    )]
    #[ORM\JoinColumn(
        name                : 'bad_house_id',
        referencedColumnName: 'id',
        nullable            : false,
        onDelete            : 'CASCADE'
    )]
    private ?BadHouse $badHouse = null;

    public function setBadHouse(?BadHouse $targetHouse): void
    {
        $this->badHouse = $targetHouse;
    }

    public function getBadHouse(): ?BadHouse
    {
        return $this->badHouse;
    }
}
