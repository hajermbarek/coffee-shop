<?php
namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Reservations;
use App\Entity\ReservationLivres;
use App\Entity\Livres;
use App\Entity\Game;
use App\Entity\Tables;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ReservationController extends AbstractController
{
    #[Route('/reservation/final', name: 'reservation_final')]
    public function final(Request $request, SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $tableId = $session->get('reservationTable');
        $date = $session->get('reservationDate');
        $time = $session->get('reservationTime');
        $activityType = $session->get('activity_type');
        $activityId = $session->get('activity_id');
        $activityName = $session->get('activity');

        if (!$activityId) {
            return $this->redirectToRoute('book_list');
        }

        if ($request->isMethod('POST')) {
            $prenom = $request->request->get('firstname');
            $nom = $request->request->get('name');
            $email = $request->request->get('email');
            $telephone = $request->request->get('phone');
            $nbPersonnes = $request->request->get('people');
            $allergies = $request->request->get('allergies');
            $commentaires = $request->request->get('comments');

            $em = $doctrine->getManager();

            // Client
            $clientRepo = $doctrine->getRepository(Clients::class);
            $client = $clientRepo->findOneBy(['email' => $email]);
            if (!$client) {
                $client = new Clients();
                $client->setPrenom($prenom);
                $client->setNom($nom);
                $client->setEmail($email);
                $client->setTelephone($telephone);
                $client->setDateInscription(new \DateTime());
                $em->persist($client);
            } else {
                $client->setPrenom($prenom);
                $client->setNom($nom);
                $client->setTelephone($telephone);
            }
            $em->flush();

            // Table
            $table = $doctrine->getRepository(Tables::class)->find($tableId);

            // Réservation
            $reservation = new Reservations();
            $reservation->setClient($client);
            $reservation->setTable($table);
            $reservation->setDateReservation(new \DateTime($date));
            $reservation->setHeureReservation(new \DateTime($time));
            $reservation->setNbPersonnes($nbPersonnes);
            $reservation->setAllergies($allergies);
            $reservation->setCommentaires($commentaires);
            $reservation->setStatut('confirmee');
            $em->persist($reservation);
            $em->flush();

            // Gestion livre
            if ($activityType === 'book') {
                $livre = $doctrine->getRepository(Livres::class)->find($activityId);
                $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiry = (new \DateTime($date))->modify('+7 days');

                $resaBook = new ReservationLivres();
                $resaBook->setReservation($reservation);
                $resaBook->setLivre($livre);
                $resaBook->setCode($code);
                $resaBook->setDateExpiration($expiry);
                $em->persist($resaBook);

                // Diminuer exemplaires disponibles
                $livre->setExemplairesDisponibles($livre->getExemplairesDisponibles() - 1);
                $em->persist($livre);
                $em->flush();

                $displayCode = $code;
            } else {
                $displayCode = null;
            }

            $session->clear();

            return $this->render('reservation/success.html.twig', [
                'code' => $displayCode ?? null,
                'expiry' => $expiry ?? null,
                'activityName' => $activityName,
                'clientName' => $prenom . ' ' . $nom,
                'date' => $date,
                'time' => $time,
                'tableNum' => $table->getNumero(),
                'zone' => $session->get('reservationZone'),
            ]);
        }

        return $this->render('reservation/final.html.twig', [
            'zone' => $session->get('reservationZone'),
            'tableId' => $tableId,
            'date' => $date,
            'time' => $time,
            'activity' => $activityName,
        ]);
    }
}