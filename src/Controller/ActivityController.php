<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class ActivityController extends AbstractController
{
    #[Route('/activity', name: 'app_activity')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activity);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'activité a été ajoutée avec succès!');
            
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('activity/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
