<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "clients")]
class Clients
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_client", type: "integer")]
    private ?int $id_client = null;

    #[ORM\Column(name: "prenom", type: "string", length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(name: "nom", type: "string", length: 50)]
    private ?string $nom = null;

    #[ORM\Column(name: "email", type: "string", length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: "telephone", type: "string", length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(name: "date_inscription", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $date_inscription = null;

    public function getIdClient(): ?int
    {
        return $this->id_client;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;
        return $this;
    }
}