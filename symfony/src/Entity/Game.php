<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "game")]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "name", type: "string", length: 100)]
    private ?string $name = null;

    #[ORM\Column(name: "description", type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(name: "category", type: "string", length: 50)]
    private ?string $category = null;

    #[ORM\Column(name: "category_color", type: "string", length: 20, options: ["default" => "#d9822b"])]
    private ?string $category_color = null;

    #[ORM\Column(name: "players", type: "string", length: 50)]
    private ?string $players = null;

    #[ORM\Column(name: "duration", type: "string", length: 50)]
    private ?string $duration = null;

    #[ORM\Column(name: "image_path", type: "string", length: 255)]
    private ?string $image_path = null;

    #[ORM\Column(name: "rules", type: Types::JSON, nullable: true)]
    private ?array $rules = null;

    #[ORM\Column(name: "exemplaires_total", type: "integer", options: ["default" => 1])]
    private ?int $exemplaires_total = null;

    #[ORM\Column(name: "exemplaires_disponibles", type: "integer", options: ["default" => 1])]
    private ?int $exemplaires_disponibles = null;

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function getCategory(): ?string { return $this->category; }
    public function setCategory(string $category): self { $this->category = $category; return $this; }
    public function getCategoryColor(): ?string { return $this->category_color; }
    public function setCategoryColor(string $category_color): self { $this->category_color = $category_color; return $this; }
    public function getPlayers(): ?string { return $this->players; }
    public function setPlayers(string $players): self { $this->players = $players; return $this; }
    public function getDuration(): ?string { return $this->duration; }
    public function setDuration(string $duration): self { $this->duration = $duration; return $this; }
    public function getImagePath(): ?string { return $this->image_path; }
    public function setImagePath(string $image_path): self { $this->image_path = $image_path; return $this; }
    public function getRules(): ?array { return $this->rules; }
    public function setRules(?array $rules): self { $this->rules = $rules; return $this; }
    public function getExemplairesTotal(): ?int { return $this->exemplaires_total; }
    public function setExemplairesTotal(int $exemplaires_total): self { $this->exemplaires_total = $exemplaires_total; return $this; }
    public function getExemplairesDisponibles(): ?int { return $this->exemplaires_disponibles; }
    public function setExemplairesDisponibles(int $exemplaires_disponibles): self { $this->exemplaires_disponibles = $exemplaires_disponibles; return $this; }
}