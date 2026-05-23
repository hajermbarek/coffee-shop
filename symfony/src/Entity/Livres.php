<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "livres")]
class Livres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_livre", type: "integer")]
    private ?int $id_livre = null;

    #[ORM\Column(name: "titre", type: "string", length: 200)]
    private ?string $titre = null;

    #[ORM\Column(name: "auteur", type: "string", length: 100, nullable: true)]
    private ?string $auteur = null;

    #[ORM\Column(name: "image", type: "string", length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "exemplaires_total", type: "integer", options: ["default" => 1])]
    private ?int $exemplaires_total = null;

    #[ORM\Column(name: "exemplaires_disponibles", type: "integer", options: ["default" => 1])]
    private ?int $exemplaires_disponibles = null;

    // Getters / setters (à générer avec make:entity --regenerate)
    public function getIdLivre(): ?int { return $this->id_livre; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): self { $this->titre = $titre; return $this; }
    public function getAuteur(): ?string { return $this->auteur; }
    public function setAuteur(?string $auteur): self { $this->auteur = $auteur; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function getExemplairesTotal(): ?int { return $this->exemplaires_total; }
    public function setExemplairesTotal(int $exemplaires_total): self { $this->exemplaires_total = $exemplaires_total; return $this; }
    public function getExemplairesDisponibles(): ?int { return $this->exemplaires_disponibles; }
    public function setExemplairesDisponibles(int $exemplaires_disponibles): self { $this->exemplaires_disponibles = $exemplaires_disponibles; return $this; }
}