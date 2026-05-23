<?php
namespace App\Controller;

use App\Entity\ReservationLivres;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VerifyCodeController extends AbstractController
{
    #[Route('/verify-code', name: 'verify_code', methods: ['POST'])]
    public function verifyCode(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $code = $request->request->get('code');
        $repo = $doctrine->getRepository(ReservationLivres::class);
        $resa = $repo->findOneBy(['code' => $code]);

        if (!$resa) {
            return $this->json(['valid' => false]);
        }

        $expired = $resa->getDateExpiration() < new \DateTime();
        return $this->json([
            'valid' => !$expired,
            'expired' => $expired,
            'expiry_date' => $resa->getDateExpiration()->format('d/m/Y'),
            'book_title' => $resa->getLivre()->getTitre(),
        ]);
    }
}