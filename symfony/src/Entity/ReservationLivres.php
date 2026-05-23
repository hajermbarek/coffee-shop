<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "reservation_livres")]
class ReservationLivres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_RL", type: "integer")]
    private ?int $id_RL = null;

    #[ORM\ManyToOne(targetEntity: Reservations::class)]
    #[ORM\JoinColumn(name: "id_reservation", referencedColumnName: "id_reservation")]
    private ?Reservations $reservation = null;

    #[ORM\ManyToOne(targetEntity: Livres::class)]
    #[ORM\JoinColumn(name: "id_livre", referencedColumnName: "id_livre")]
    private ?Livres $livre = null;

    #[ORM\Column(name: "code", type: "string", length: 50, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(name: "date_expiration", type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_expiration = null;

    // Getters / setters
    public function getIdRL(): ?int { return $this->id_RL; }
    public function getReservation(): ?Reservations { return $this->reservation; }
    public function setReservation(?Reservations $reservation): self { $this->reservation = $reservation; return $this; }
    public function getLivre(): ?Livres { return $this->livre; }
    public function setLivre(?Livres $livre): self { $this->livre = $livre; return $this; }
    public function getCode(): ?string { return $this->code; }
    public function setCode(?string $code): self { $this->code = $code; return $this; }
    public function getDateExpiration(): ?\DateTimeInterface { return $this->date_expiration; }
    public function setDateExpiration(?\DateTimeInterface $date_expiration): self { $this->date_expiration = $date_expiration; return $this; }
}