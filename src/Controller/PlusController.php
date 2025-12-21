<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PlusController extends AbstractController
{
    #[Route('/plus', name: 'app_plus')]
    public function index(): Response
    {
        return $this->render('plus/plus.html.twig', [
            'controller_name' => 'PlusController',
        ]);
    }
}
