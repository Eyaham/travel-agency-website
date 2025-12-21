<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Form\HotelType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class HotelController extends AbstractController
{
    #[Route('/hotel', name: 'app_hotel')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hotel);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'hôtel a été ajouté avec succès!');
            
            return $this->redirectToRoute('app_destination');
        }
        
        return $this->render('hotel/hotel.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
