<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "reservations")]
class Reservations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_reservation", type: "integer")]
    private ?int $id_reservation = null;

    #[ORM\ManyToOne(targetEntity: Clients::class)]
    #[ORM\JoinColumn(name: "id_client", referencedColumnName: "id_client")]
    private ?Clients $client = null;

    #[ORM\ManyToOne(targetEntity: Tables::class)]
    #[ORM\JoinColumn(name: "id_table", referencedColumnName: "id_table")]
    private ?Tables $table = null;

    #[ORM\Column(name: "date_reservation", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_reservation = null;

    #[ORM\Column(name: "heure_reservation", type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure_reservation = null;

    #[ORM\Column(name: "nb_personnes", type: "integer")]
    private ?int $nb_personnes = null;

    #[ORM\Column(name: "allergies", type: Types::TEXT, nullable: true)]
    private ?string $allergies = null;

    #[ORM\Column(name: "commentaires", type: Types::TEXT, nullable: true)]
    private ?string $commentaires = null;

    #[ORM\Column(name: "statut", type: "string", length: 20, options: ["default" => "confirmee"])]
    private ?string $statut = null;

    // Getters / setters
    public function getIdReservation(): ?int { return $this->id_reservation; }
    public function getClient(): ?Clients { return $this->client; }
    public function setClient(?Clients $client): self { $this->client = $client; return $this; }
    public function getTable(): ?Tables { return $this->table; }
    public function setTable(?Tables $table): self { $this->table = $table; return $this; }
    public function getDateReservation(): ?\DateTimeInterface { return $this->date_reservation; }
    public function setDateReservation(\DateTimeInterface $date_reservation): self { $this->date_reservation = $date_reservation; return $this; }
    public function getHeureReservation(): ?\DateTimeInterface { return $this->heure_reservation; }
    public function setHeureReservation(\DateTimeInterface $heure_reservation): self { $this->heure_reservation = $heure_reservation; return $this; }
    public function getNbPersonnes(): ?int { return $this->nb_personnes; }
    public function setNbPersonnes(int $nb_personnes): self { $this->nb_personnes = $nb_personnes; return $this; }
    public function getAllergies(): ?string { return $this->allergies; }
    public function setAllergies(?string $allergies): self { $this->allergies = $allergies; return $this; }
    public function getCommentaires(): ?string { return $this->commentaires; }
    public function setCommentaires(?string $commentaires): self { $this->commentaires = $commentaires; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
}