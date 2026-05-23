<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "reservation_jeux")]
class ReservationJeux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_RJ", type: "integer")]
    private ?int $id_RJ = null;

    #[ORM\ManyToOne(targetEntity: Reservations::class)]
    #[ORM\JoinColumn(name: "id_reservation", referencedColumnName: "id_reservation")]
    private ?Reservations $reservation = null;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name: "id_game", referencedColumnName: "id")]
    private ?Game $game = null;

    // Getters / setters
    public function getIdRJ(): ?int { return $this->id_RJ; }
    public function getReservation(): ?Reservations { return $this->reservation; }
    public function setReservation(?Reservations $reservation): self { $this->reservation = $reservation; return $this; }
    public function getGame(): ?Game { return $this->game; }
    public function setGame(?Game $game): self { $this->game = $game; return $this; }
}