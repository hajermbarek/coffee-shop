<?php
namespace App\Controller;

use App\Entity\Livres;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/books', name: 'book_list')]
    public function list(ManagerRegistry $doctrine): Response
    {
        $livres = $doctrine->getRepository(Livres::class)->findAll();
        return $this->render('book/list.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/book/reserve/{id_livre}', name: 'book_reserve', methods: ['POST'])]
    public function reserve(ManagerRegistry $doctrine, SessionInterface $session, int $id_livre): Response
    {
        $livre = $doctrine->getRepository(Livres::class)->find($id_livre);
        if (!$livre) {
            throw $this->createNotFoundException('Livre non trouve');
        }

        $session->set('activity', $livre->getTitre());
        $session->set('activity_type', 'book');
        $session->set('activity_id', $livre->getIdLivre());
        $this->addFlash('success', 'Livre selectionne : ' . $livre->getTitre());

        if ($session->get('reservationTable')) {
            return $this->redirectToRoute('reservation_form');
        }
        return $this->redirectToRoute('seating_quiet');
    }

    #[Route('/book/{id_livre}', name: 'book_detail')]
    public function detail(ManagerRegistry $doctrine, int $id_livre): Response
    {
        $livre = $doctrine->getRepository(Livres::class)->find($id_livre);
        if (!$livre) {
            throw $this->createNotFoundException('Livre non trouve');
        }
        return $this->render('book/detail.html.twig', [
            'livre' => $livre,
        ]);
    }
}