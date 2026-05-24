<?php

namespace App\Controller;

use App\Entity\Tables;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SeatingController extends AbstractController
{
    #[Route('/seating/quiet', name: 'seating_quiet')]
    public function quietZone(ManagerRegistry $doctrine): Response
    {
        $tables = $doctrine->getRepository(Tables::class)->findBy(['zone' => 1]);
        return $this->render('seating/quiet.html.twig', [
            'tables' => $tables,
        ]);
    }

    #[Route('/seating/quiet/select', name: 'seating_quiet_select', methods: ['POST'])]
    public function selectQuietTable(Request $request, SessionInterface $session): Response
    {
        $tableId = $request->request->get('table_id');
        $date = $request->request->get('date');
        $time = $request->request->get('time');

        if (!$tableId || !$date || !$time) {
            $this->addFlash('error', 'Veuillez sélectionner une table, une date et une heure.');
            return $this->redirectToRoute('seating_quiet');
        }

        $session->set('reservationTable', $tableId);
        $session->set('table_id', $tableId);  // AJOUT : pour compatibilité
        $session->set('reservationDate', $date);
        $session->set('reservationTime', $time);
        $session->set('reservationZone', 'Quiet Zone');

        return $this->redirectToRoute('book_list');
    }

    #[Route('/seating/fun', name: 'seating_fun')]
    public function funZone(ManagerRegistry $doctrine): Response
    {
        $tables = $doctrine->getRepository(Tables::class)->findBy(['zone' => 2]);
        return $this->render('seating/fun.html.twig', [
            'tables' => $tables,
        ]);
    }

    #[Route('/seating/fun/select', name: 'seating_fun_select', methods: ['POST'])]
    public function selectFunTable(Request $request, SessionInterface $session): Response
    {
        $tableId = $request->request->get('table_id');
        $date = $request->request->get('date');
        $time = $request->request->get('time');

        if (!$tableId || !$date || !$time) {
            $this->addFlash('error', 'Veuillez sélectionner une table, une date et une heure.');
            return $this->redirectToRoute('seating_fun');
        }

        $session->set('reservationTable', $tableId);
        $session->set('table_id', $tableId);  // AJOUT : pour compatibilité
        $session->set('reservationDate', $date);
        $session->set('reservationTime', $time);
        $session->set('reservationZone', 'Fun Zone');

        return $this->redirectToRoute('games_list');
    }
    #[Route('/seating/quiet/availability', name: 'seating_quiet_availability')]
    public function checkQuietAvailability(Request $request, ManagerRegistry $doctrine): Response
    {
        $date = $request->query->get('date');
        $time = $request->query->get('time');
        // query your reservations here and return JSON
        return $this->json(['reserved' => [/* table ids */]]);
    }
}
