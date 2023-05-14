<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: ServiceRepository::class)]
#[Table(name: '`service`')]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 256, unique: true, nullable: false)]
    public string $name = '';

    #[ORM\Column(type: 'string', length: 256, unique: false, nullable: false)]
    public string $price = '';

    #[ORM\Column(type: 'string', length: 256, unique: false, nullable: false)]
    public string $description = '';

    // One Medic has many Services.
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'services')]
    #[ORM\JoinColumn(name: 'medic_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $medic;

    #[ORM\Column(type: 'integer', length: 256, unique: false)]
    public int $duration = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMedic(): ?User
    {
        return $this->medic;
    }

    public function setMedic(?User $medic): self
    {
        $this->medic = $medic;

        return $this;
    }
}
