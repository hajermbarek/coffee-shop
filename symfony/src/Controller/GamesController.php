<?php
namespace App\Controller;

use App\Entity\Game;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends AbstractController
{
    #[Route('/games', name: 'games_list')]
    public function list(ManagerRegistry $doctrine, Request $request): Response
    {
        $category = $request->query->get('category', 'ALL');
        $search   = trim($request->query->get('search', ''));

        $qb = $doctrine->getRepository(Game::class)->createQueryBuilder('g');
        if ($category !== 'ALL') {
            $qb->andWhere('g.category = :cat')->setParameter('cat', $category);
        }
        if ($search !== '') {
            $qb->andWhere('g.name LIKE :search OR g.description LIKE :search')
               ->setParameter('search', "%$search%");
        }
        $games = $qb->orderBy('g.name', 'ASC')->getQuery()->getResult();

        return $this->render('games/list.html.twig', [
            'games'      => $games,
            'category'   => $category,
            'search'     => $search,
            'categories' => ['ALL', 'FUN', 'STRATEGY', 'CLASSIC GAME'],
        ]);
    }

    #[Route('/game/check-stock/{id}', name: 'game_check_stock')]
    public function checkStock(Game $game): JsonResponse
    {
        return new JsonResponse([
            'disponibles' => $game->getExemplairesDisponibles()
        ]);
    }

    #[Route('/game/{id}', name: 'game_detail')]
    public function detail(Game $game): Response
    {
        return $this->render('games/detail.html.twig', ['game' => $game]);
    }

    #[Route('/game/reserve/{id}', name: 'game_reserve', methods: ['POST'])]
    public function reserve(Game $game, SessionInterface $session): Response
    {
        $session->set('activity', $game->getName());
        $session->set('activity_type', 'game');
        $session->set('activity_id', $game->getId());
        $this->addFlash('success', 'Jeu sélectionné : ' . $game->getName());

        if ($session->get('reservationTable')) {
            return $this->redirectToRoute('reservation_final');
        }
        return $this->redirectToRoute('seating_fun');
    }
}