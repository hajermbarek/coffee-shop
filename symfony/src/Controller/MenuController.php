<?php
namespace App\Controller;

use App\Entity\MenuItems;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'menu_main')]
    public function main(): Response
    {
        return $this->render('menu/index.html.twig');
    }

    #[Route('/menu/hot-drinks', name: 'menu_hot')]
    public function hotDrinks(ManagerRegistry $doctrine): Response
    {
        $items = $doctrine->getRepository(MenuItems::class)
            ->findBy(['categorySlug' => 'hot-drinks']);
        return $this->render('menu/hot.html.twig', ['items' => $items]);
    }

    #[Route('/menu/cold-drinks', name: 'menu_cold')]
    public function coldDrinks(ManagerRegistry $doctrine): Response
    {
        $items = $doctrine->getRepository(MenuItems::class)
            ->findBy(['categorySlug' => 'cold-drinks']);
        return $this->render('menu/cold.html.twig', ['items' => $items]);
    }

    #[Route('/menu/blended', name: 'menu_blended')]
    public function blended(ManagerRegistry $doctrine): Response
    {
        $items = $doctrine->getRepository(MenuItems::class)
            ->findBy(['categorySlug' => 'blended']);
        return $this->render('menu/blended.html.twig', ['items' => $items]);
    }

    #[Route('/menu/pastry', name: 'menu_pastry')]
    public function pastry(ManagerRegistry $doctrine): Response
    {
        $items = $doctrine->getRepository(MenuItems::class)
            ->findBy(['categorySlug' => 'pastry']);
        return $this->render('menu/pastry.html.twig', ['items' => $items]);
    }

    #[Route('/menu/cake', name: 'menu_cake')]
    public function cake(ManagerRegistry $doctrine): Response
    {
        $items = $doctrine->getRepository(MenuItems::class)
            ->findBy(['categorySlug' => 'cake']);
        return $this->render('menu/cake.html.twig', ['items' => $items]);
    }

    #[Route('/menu/biscuit', name: 'menu_biscuit')]
    public function biscuit(ManagerRegistry $doctrine): Response
    {
        $items = $doctrine->getRepository(MenuItems::class)
            ->findBy(['categorySlug' => 'biscuit']);
        return $this->render('menu/biscuit.html.twig', ['items' => $items]);
    }
}
