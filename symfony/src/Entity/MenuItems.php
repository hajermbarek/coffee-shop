<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "menu_items")]
class MenuItems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "category_slug", type: "string", length: 50)]
    private ?string $category_slug = null;

    #[ORM\Column(name: "name", type: "string", length: 150)]
    private ?string $name = null;

    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "price", type: "string", length: 50)]
    private ?string $price = null;

    #[ORM\Column(name: "image_url", type: "string", length: 255, nullable: true)]
    private ?string $image_url = null;

    #[ORM\Column(name: "is_popular", type: "boolean", options: ["default" => false])]
    private ?bool $is_popular = false;

    // Getters / setters...
}