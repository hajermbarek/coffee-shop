<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MenuItemsRepository;

#[ORM\Entity(repositoryClass: MenuItemsRepository::class)]
#[ORM\Table(name: "menu_items")]
class MenuItems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "category_slug", type: "string", length: 50)]
    private ?string $categorySlug = null;

    #[ORM\Column(name: "name", type: "string", length: 150)]
    private ?string $name = null;

    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "price", type: "string", length: 50)]
    private ?string $price = null;

    #[ORM\Column(name: "image_url", type: "string", length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(name: "is_popular", type: "boolean", options: ["default" => false])]
    private bool $isPopular = false;

    // --- Getters & Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorySlug(): ?string
    {
        return $this->categorySlug;
    }

    public function setCategorySlug(string $categorySlug): static
    {
        $this->categorySlug = $categorySlug;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function isPopular(): bool
    {
        return $this->isPopular;
    }

    public function setIsPopular(bool $isPopular): static
    {
        $this->isPopular = $isPopular;
        return $this;
    }
}
