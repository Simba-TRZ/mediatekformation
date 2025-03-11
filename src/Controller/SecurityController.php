<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        throw new \Exception('Cette méthode ne devrait jamais être atteinte, Symfony gère la déconnexion.');
    }
}
