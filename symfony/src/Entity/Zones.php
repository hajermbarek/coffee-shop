<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "zones")]
class Zones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_zone", type: "integer")]
    private ?int $id_zone = null;

    #[ORM\Column(name: "nom", type: "string", length: 50)]
    private ?string $nom = null;

    public function getIdZone(): ?int { return $this->id_zone; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
}