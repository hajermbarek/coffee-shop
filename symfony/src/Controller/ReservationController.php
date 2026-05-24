<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\ReservationLivres;
use App\Entity\ReservationJeux;
use App\Entity\Livres;
use App\Entity\Tables;
use App\Entity\Reservations;
use App\Entity\Game;
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
        $session = $request->getSession();
        $zone = $session->get('reservationZone', 'Non spécifiée');
        $tableNum = $session->get('reservationTable', '—');
        $tableId = $session->get('reservationTable');
        $dateResa = $session->get('reservationDate');
        $heureResa = $session->get('reservationTime');
        $activityType = $session->get('activity_type');
        $activityId = $session->get('activity_id');
        $activity = $session->get('activity', 'Aucune activité choisie');

        // Code pré‑rempli
        $prefilledCode = '';
        $codeActivity = '';
        if ($codeParam = $request->query->get('code')) {
            $rl = $em->getRepository(ReservationLivres::class)
                ->findOneBy(['code' => $codeParam]);
            if ($rl && $rl->getDateExpiration() > new \DateTime()) {
                $prefilledCode = $codeParam;
                $codeActivity = $rl->getLivre()->getTitre();
                if (!$activityType) {
                    $activity = $codeActivity;
                    $activityType = 'book';
                    $activityId = $rl->getLivre()->getIdLivre();
                }
            }
        }

        // Traitement POST
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

            if (!$prenom || !$nom || !$email || !$telephone || !$tableId || !$dateResa || !$heureResa) {
                $this->addFlash('error', 'Tous les champs obligatoires doivent être remplis.');
                return $this->redirectToRoute('reservation_form');
            }

            try {
                // Client
                $client = $em->getRepository(Clients::class)->findOneBy(['email' => $email]);
                if (!$client) {
                    $client = new Clients();
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

                // Table
                $table = $em->getRepository(Tables::class)->find($tableId);
                if (!$table) throw new \Exception("Table non trouvée");

                // Réservation principale
                $reservation = new Reservations();
                $reservation->setClient($client);
                $reservation->setTable($table);
                $reservation->setDateReservation(new \DateTime($dateResa));
                // L'heure doit être un objet DateTime
                $heureObj = \DateTime::createFromFormat('H:i', $heureResa);
                if (!$heureObj) throw new \Exception("Format d'heure invalide");
                $reservation->setHeureReservation($heureObj);
                $reservation->setNbPersonnes($nbPersonnes);
                $reservation->setAllergies($allergies);
                $reservation->setCommentaires($commentaires);
                $reservation->setStatut('confirmee');
                $em->persist($reservation);
                $em->flush();

                // Gestion code existant ou nouvelle réservation livre/jeu
                if ($hasCode && $codeSaisi) {
                    $rl = $em->getRepository(ReservationLivres::class)->findOneBy(['code' => $codeSaisi]);
                    if (!$rl) throw new \Exception("Code invalide.");
                    $rl->setReservation($reservation);
                    $em->flush();
                } elseif ($activityType === 'book') {
                    $livre = $em->getRepository(Livres::class)->find($activityId);
                    if (!$livre) throw new \Exception("Livre non trouvé");

                    // Vérifier si ce client a déjà réservé ce livre récemment
                    $existing = $em->getRepository(ReservationLivres::class)
                        ->createQueryBuilder('rl')
                        ->join('rl.reservation', 'r')
                        ->where('rl.livre = :livre')
                        ->andWhere('r.client = :client')
                        ->andWhere('r.statut = :statut')
                        ->andWhere('r.date_reservation >= :date')   // correction ici
                        ->setParameter('livre', $livre)
                        ->setParameter('client', $client)
                        ->setParameter('statut', 'confirmee')
                        ->setParameter('date', (new \DateTime())->modify('-7 days'))
                        ->getQuery()
                        ->getOneOrNullResult();

                    if ($existing) {
                        $existing->setReservation($reservation);
                        $em->flush();
                    } else {
                        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                        $expiration = (clone $reservation->getDateReservation())->modify('+7 days');
                        $rl = new ReservationLivres();
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

                    // Vérifier la disponibilité du jeu à cette date/heure
                    $dateObj = new \DateTime($dateResa);
                    $start = (clone $dateObj)->setTime((int)substr($heureResa,0,2), (int)substr($heureResa,3,2))->modify('-3 hours');
                    $end   = (clone $dateObj)->setTime((int)substr($heureResa,0,2), (int)substr($heureResa,3,2))->modify('+3 hours');

                    $count = $em->createQueryBuilder()
                        ->select('COUNT(rj.id_RJ)')
                        ->from(ReservationJeux::class, 'rj')
                        ->join('rj.reservation', 'r')
                        ->where('rj.game = :game')
                        ->andWhere('r.statut = :statut')
                        ->andWhere('r.date_reservation = :date')
                        ->andWhere('r.heure_reservation BETWEEN :start AND :end')   // corrections ici
                        ->setParameter('game', $game)
                        ->setParameter('statut', 'confirmee')
                        ->setParameter('date', $dateObj)
                        ->setParameter('start', $start->format('H:i:s'))
                        ->setParameter('end', $end->format('H:i:s'))
                        ->getQuery()
                        ->getSingleScalarResult();

                    if ($count >= $game->getExemplairesTotal()) {
                        throw new \Exception("Ce jeu n'est plus disponible à cette date et heure.");
                    }
                    $rj = new ReservationJeux();
                    $rj->setReservation($reservation);
                    $rj->setGame($game);
                    $em->persist($rj);
                    $em->flush();
                } else {
                    throw new \Exception("Type d'activité non reconnu.");
                }

                // Nettoyer session
                $session->remove('reservationZone');
                $session->remove('reservationTable');
                $session->remove('table_id');
                $session->remove('reservationDate');
                $session->remove('reservationTime');
                $session->remove('activity');
                $session->remove('activity_type');
                $session->remove('activity_id');

                return $this->redirectToRoute('reservation_confirmation', ['id' => $reservation->getIdReservation()]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('reservation_form');
            }
        }

        // Affichage du formulaire
        return $this->render('reservation/form.html.twig', [
            'zone' => $zone,
            'tableNum' => $tableNum,
            'tableId' => $tableId,
            'dateResa' => $dateResa,
            'heureResa' => $heureResa,
            'activity' => $activity,
            'activityType' => $activityType,
            'activityId' => $activityId,
            'prefilledCode' => $prefilledCode,
            'codeActivity' => $codeActivity,
        ]);
    }

    #[Route('/confirmation/{id}', name: 'reservation_confirmation')]
    public function confirmation(Reservations $reservation, EntityManagerInterface $em): Response
    {
        $code = null;
        $dateExpiration = null;
        $activityName = '';
        $activityType = '';

        $rl = $em->getRepository(ReservationLivres::class)->findOneBy(['reservation' => $reservation]);
        if ($rl) {
            $code = $rl->getCode();
            $dateExpiration = $rl->getDateExpiration();
            $activityName = $rl->getLivre()->getTitre();
            $activityType = 'book';
        } else {
            $rj = $em->getRepository(ReservationJeux::class)->findOneBy(['reservation' => $reservation]);
            if ($rj) {
                $activityName = $rj->getGame()->getName();
                $activityType = 'game';
            }
        }

        $client = $reservation->getClient();

        return $this->render('reservation/confirmation.html.twig', [
            'reservation'    => $reservation,
            'code'           => $code,
            'expiry'         => $dateExpiration,
            'activityName'   => $activityName,
            'activityType'   => $activityType,
            'clientName'     => $client->getPrenom() . ' ' . $client->getNom(),
            'email'          => $client->getEmail(),
            'phone'          => $client->getTelephone(),
            'date'           => $reservation->getDateReservation()->format('d/m/Y'),
            'time'           => $reservation->getHeureReservation()->format('H:i'),
            'tableNum'       => $reservation->getTable()->getNumero(),
            'zone'           => $reservation->getTable()->getZone()->getNom(),
            'people'         => $reservation->getNbPersonnes(),
        ]);
    }

    #[Route('/verifier-code', name: 'reservation_verify_code', methods: ['POST'])]
    public function verifyCode(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $code = $request->request->get('code');
        if (!preg_match('/^\d{6}$/', $code)) {
            return $this->json(['valid' => false]);
        }

        $rl = $em->getRepository(ReservationLivres::class)->findOneBy(['code' => $code]);
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