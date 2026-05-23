<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tables")]
class Tables
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_table", type: "integer")]
    private ?int $id_table = null;

    #[ORM\Column(name: "numero", type: "integer")]
    private ?int $numero = null;

    #[ORM\Column(name: "places", type: "integer")]
    private ?int $places = null;

    #[ORM\ManyToOne(targetEntity: Zones::class)]
    #[ORM\JoinColumn(name: "id_zone", referencedColumnName: "id_zone")]
    private ?Zones $zone = null;

    public function getIdTable(): ?int { return $this->id_table; }
    public function getNumero(): ?int { return $this->numero; }
    public function setNumero(int $numero): self { $this->numero = $numero; return $this; }
    public function getPlaces(): ?int { return $this->places; }
    public function setPlaces(int $places): self { $this->places = $places; return $this; }
    public function getZone(): ?Zones { return $this->zone; }
    public function setZone(?Zones $zone): self { $this->zone = $zone; return $this; }
}