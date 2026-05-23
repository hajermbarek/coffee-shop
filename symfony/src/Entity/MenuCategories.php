<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "menu_categories")]
class MenuCategories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "section", type: "string", length: 20)]
    private ?string $section = null;

    #[ORM\Column(name: "title", type: "string", length: 100)]
    private ?string $title = null;

    #[ORM\Column(name: "image_url", type: "string", length: 255)]
    private ?string $image_url = null;

    #[ORM\Column(name: "link", type: "string", length: 100)]
    private ?string $link = null;

    // Getters / setters
    public function getId(): ?int { return $this->id; }
    public function getSection(): ?string { return $this->section; }
    public function setSection(string $section): self { $this->section = $section; return $this; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getImageUrl(): ?string { return $this->image_url; }
    public function setImageUrl(string $image_url): self { $this->image_url = $image_url; return $this; }
    public function getLink(): ?string { return $this->link; }
    public function setLink(string $link): self { $this->link = $link; return $this; }
}