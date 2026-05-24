<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Reservation;
use App\Entity\ReservationLivre;
use App\Entity\ReservationJeu;
use App\Entity\Livre;
use App\Entity\Game;
use App\Entity\Table;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reserver', name: 'reservation_form', methods: ['GET', 'POST'])]
    public function formulaire(Request $request, EntityManagerInterface $em): Response
    {
        // 1. Récupération des infos de session (zone, table, date, heure, activité)
        $session = $request->getSession();
        $zone = $session->get('reservationZone', 'Non spécifiée');
        $tableNum = $session->get('reservationTable', '—');
        $tableId = $session->get('table_id');
        $dateResa = $session->get('reservationDate');
        $heureResa = $session->get('reservationTime');
        $activityType = $session->get('activity_type');
        $activityId = $session->get('activity_id');
        $activity = $session->get('activity', 'Aucune activité choisie');

        // 2. Pré‑remplir le champ code si un code est passé dans l'URL (ex: ?code=123456)
        $prefilledCode = '';
        $codeActivity = '';
        if ($codeParam = $request->query->get('code')) {
            $rl = $em->getRepository(ReservationLivre::class)
                ->findOneBy(['code' => $codeParam]);
            if ($rl && $rl->getDateExpiration() > new \DateTime()) {
                $prefilledCode = $codeParam;
                $codeActivity = $rl->getLivre()->getTitre();
                // Si aucune activité en session, on prend celle du code
                if (!$activityType) {
                    $activity = $codeActivity;
                    $activityType = 'book';
                    $activityId = $rl->getLivre()->getId();
                }
            }
        }

        // 3. Traitement du formulaire soumis
        if ($request->isMethod('POST')) {
            $prenom = $request->request->get('firstname');
            $nom = $request->request->get('name');
            $email = $request->request->get('email');
            $telephone = $request->request->get('phone');
            $nbPersonnes = $request->request->getInt('people', 1);
            $allergies = $request->request->get('allergies', '');
            $commentaires = $request->request->get('comments', '');
            $codeSaisi = trim($request->request->get('reservation_code', ''));
            $hasCode = $request->request->getBoolean('has_code');

            // Validation simple
            if (!$prenom || !$nom || !$email || !$telephone || !$tableId || !$dateResa || !$heureResa) {
                $this->addFlash('error', 'Tous les champs obligatoires doivent être remplis.');
                return $this->redirectToRoute('reservation_form');
            }

            try {
                // === Gestion du client ===
                $client = $em->getRepository(Client::class)->findOneBy(['email' => $email]);
                if (!$client) {
                    $client = new Client();
                    $client->setPrenom($prenom);
                    $client->setNom($nom);
                    $client->setEmail($email);
                    $client->setTelephone($telephone);
                    $em->persist($client);
                } else {
                    $client->setPrenom($prenom);
                    $client->setNom($nom);
                    $client->setTelephone($telephone);
                }
                $em->flush();

                // === Récupération de la table ===
                $table = $em->getRepository(Table::class)->find($tableId);
                if (!$table) throw new \Exception("Table non trouvée");

                // === Création de la réservation de table ===
                $reservation = new Reservation();
                $reservation->setClient($client);
                $reservation->setTable($table);
                $reservation->setDateReservation(new \DateTime($dateResa));
                $reservation->setHeureReservation($heureResa);
                $reservation->setNbPersonnes($nbPersonnes);
                $reservation->setAllergies($allergies);
                $reservation->setCommentaires($commentaires);
                $reservation->setStatut('confirmee');
                $em->persist($reservation);
                $em->flush();

                // === Traitement selon le type d'activité et la présence d'un code ===
                if ($hasCode && $codeSaisi) {
                    // Réutilisation d'un code existant
                    $rl = $em->getRepository(ReservationLivre::class)->findOneBy(['code' => $codeSaisi]);
                    if (!$rl) throw new \Exception("Code invalide.");
                    $rl->setReservation($reservation); // lier à la nouvelle réservation
                    $em->flush();
                } elseif ($activityType === 'book') {
                    $livre = $em->getRepository(Livre::class)->find($activityId);
                    if (!$livre) throw new \Exception("Livre non trouvé");

                    // Vérifier si déjà emprunté par le même client dans les 7 jours
                    $existing = $em->getRepository(ReservationLivre::class)
                        ->createQueryBuilder('rl')
                        ->join('rl.reservation', 'r')
                        ->where('rl.livre = :livre')
                        ->andWhere('r.client = :client')
                        ->andWhere('r.statut = :statut')
                        ->andWhere('r.dateReservation >= :date')
                        ->setParameter('livre', $livre)
                        ->setParameter('client', $client)
                        ->setParameter('statut', 'confirmee')
                        ->setParameter('date', (new \DateTime())->modify('-7 days'))
                        ->getQuery()
                        ->getOneOrNullResult();

                    if ($existing) {
                        // Même client, même livre : on réutilise l'enregistrement
                        $existing->setReservation($reservation);
                        $em->flush();
                    } else {
                        // Nouvel emprunt : générer code, date expiration, décrémenter stock
                        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                        $expiration = (clone $reservation->getDateReservation())->modify('+7 days');
                        $rl = new ReservationLivre();
                        $rl->setReservation($reservation);
                        $rl->setLivre($livre);
                        $rl->setCode($code);
                        $rl->setDateExpiration($expiration);
                        $em->persist($rl);
                        $livre->setExemplairesDisponibles($livre->getExemplairesDisponibles() - 1);
                        $em->flush();
                    }
                } elseif ($activityType === 'game') {
                    $game = $em->getRepository(Game::class)->find($activityId);
                    if (!$game) throw new \Exception("Jeu non trouvé");

                    // Vérifier la disponibilité sur le créneau horaire
                    $start = (new \DateTime($dateResa . ' ' . $heureResa))->modify('-3 hours');
                    $end   = (new \DateTime($dateResa . ' ' . $heureResa))->modify('+3 hours');
                    $count = $em->createQueryBuilder()
                        ->select('COUNT(rj.id)')
                        ->from(ReservationJeu::class, 'rj')
                        ->join('rj.reservation', 'r')
                        ->where('rj.game = :game')
                        ->andWhere('r.statut = :statut')
                        ->andWhere('r.dateReservation = :date')
                        ->andWhere('r.heureReservation BETWEEN :start AND :end')
                        ->setParameter('game', $game)
                        ->setParameter('statut', 'confirmee')
                        ->setParameter('date', new \DateTime($dateResa))
                        ->setParameter('start', $start->format('H:i:s'))
                        ->setParameter('end', $end->format('H:i:s'))
                        ->getQuery()
                        ->getSingleScalarResult();
                    if ($count >= $game->getExemplairesTotal()) {
                        throw new \Exception("Ce jeu n'est plus disponible à cette date et heure.");
                    }
                    $rj = new ReservationJeu();
                    $rj->setReservation($reservation);
                    $rj->setGame($game);
                    $em->persist($rj);
                    $em->flush();
                } else {
                    throw new \Exception("Type d'activité non reconnu.");
                }

                // Nettoyer la session
                $session->remove('reservationZone');
                $session->remove('reservationTable');
                $session->remove('table_id');
                $session->remove('reservationDate');
                $session->remove('reservationTime');
                $session->remove('activity');
                $session->remove('activity_type');
                $session->remove('activity_id');

                return $this->redirectToRoute('reservation_confirmation', ['id' => $reservation->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('reservation_form');
            }
        }

        // Affichage du formulaire
        return $this->render('reservation/form.html.twig', [
            'zone' => $zone,
            'tableNum' => $tableNum,
            'dateResa' => $dateResa,
            'heureResa' => $heureResa,
            'activity' => $activity,
            'prefilledCode' => $prefilledCode,
            'codeActivity' => $codeActivity,
        ]);
    }

    #[Route('/confirmation/{id}', name: 'reservation_confirmation')]
    public function confirmation(Reservation $reservation, EntityManagerInterface $em): Response
    {
        $code = null;
        $dateExpiration = null;
        $activityName = '';
        $activityType = '';

        $rl = $reservation->getReservationLivres()->first();
        if ($rl) {
            $code = $rl->getCode();
            $dateExpiration = $rl->getDateExpiration();
            $activityName = $rl->getLivre()->getTitre();
            $activityType = 'book';
        } else {
            $rj = $reservation->getReservationJeux()->first();
            if ($rj) {
                $activityName = $rj->getGame()->getName();
                $activityType = 'game';
            }
        }

        return $this->render('reservation/confirmation.html.twig', [
            'reservation' => $reservation,
            'code' => $code,
            'dateExpiration' => $dateExpiration,
            'activityName' => $activityName,
            'activityType' => $activityType,
        ]);
    }

    #[Route('/verifier-code', name: 'reservation_verify_code', methods: ['POST'])]
    public function verifyCode(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $code = $request->request->get('code');
        if (!preg_match('/^\d{6}$/', $code)) {
            return $this->json(['valid' => false]);
        }

        $rl = $em->getRepository(ReservationLivre::class)->findOneBy(['code' => $code]);
        if (!$rl) {
            return $this->json(['valid' => false]);
        }

        $expired = $rl->getDateExpiration() < new \DateTime();
        return $this->json([
            'valid' => !$expired,
            'expired' => $expired,
            'expiry_date' => $rl->getDateExpiration()->format('d/m/Y'),
            'book_title' => $rl->getLivre()->getTitre(),
        ]);
    }
}