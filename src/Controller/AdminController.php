<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Contact;
use App\Entity\Destination;
use App\Entity\Hotel;
use App\Entity\Reservation;
use App\Form\ActivityType;
use App\Form\DestinationType;
use App\Form\HotelType;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $hotels = $entityManager->getRepository(Hotel::class)->findAll();
        $activities = $entityManager->getRepository(Activity::class)->findAll();
        $destinations = $entityManager->getRepository(Destination::class)->findAll();
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();
        $contacts = $entityManager->getRepository(Contact::class)->findAll();

        // Calculate statistics
        $stats = [
            'total_reservations' => count($reservations),
            'total_clients' => count(array_unique(array_map(function($r) { return $r->getEmail(); }, $reservations))),
            'total_hotels' => count($hotels),
            'total_activities' => count($activities),
            'total_destinations' => count($destinations),
            'total_contacts' => count($contacts),
            'recent_contacts' => array_filter($contacts, function($c) {
                return $c->getCreatedAt() && $c->getCreatedAt() > new \DateTime('-7 days');
            }),
        ];

        return $this->render('admin/index.html.twig', [
            'hotels' => $hotels,
            'activities' => $activities,
            'destinations' => $destinations,
            'reservations' => $reservations,
            'contacts' => $contacts,
            'stats' => $stats,
        ]);
    }

    #[Route('/admin/hotel/new', name: 'app_admin_hotel_new', methods: ['GET', 'POST'])]
    public function hotelNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hotel);
            $entityManager->flush();

            $this->addFlash('success', "L'hôtel a été ajouté avec succès!");

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/form.html.twig', [
            'title' => 'Ajouter un hôtel',
            'form' => $form->createView(),
            'back_route' => 'app_admin',
        ]);
    }

    #[Route('/admin/hotel/{id}/edit', name: 'app_admin_hotel_edit', methods: ['GET', 'POST'])]
    public function hotelEdit(Request $request, Hotel $hotel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', "L'hôtel a été modifié avec succès!");

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/form.html.twig', [
            'title' => "Modifier l'hôtel",
            'form' => $form->createView(),
            'back_route' => 'app_admin',
        ]);
    }

    #[Route('/admin/hotel/{id}/delete', name: 'app_admin_hotel_delete', methods: ['POST'])]
    public function hotelDelete(Request $request, Hotel $hotel, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_hotel_' . $hotel->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_admin');
        }

        $entityManager->remove($hotel);
        $entityManager->flush();

        $this->addFlash('success', "L'hôtel a été supprimé avec succès!");

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/activity/new', name: 'app_admin_activity_new', methods: ['GET', 'POST'])]
    public function activityNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activity);
            $entityManager->flush();

            $this->addFlash('success', "L'activité a été ajoutée avec succès!");

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/form.html.twig', [
            'title' => 'Ajouter une activité',
            'form' => $form->createView(),
            'back_route' => 'app_admin',
        ]);
    }

    #[Route('/admin/activity/{id}/edit', name: 'app_admin_activity_edit', methods: ['GET', 'POST'])]
    public function activityEdit(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', "L'activité a été modifiée avec succès!");

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/form.html.twig', [
            'title' => "Modifier l'activité",
            'form' => $form->createView(),
            'back_route' => 'app_admin',
        ]);
    }

    #[Route('/admin/activity/{id}/delete', name: 'app_admin_activity_delete', methods: ['POST'])]
    public function activityDelete(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_activity_' . $activity->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_admin');
        }

        $entityManager->remove($activity);
        $entityManager->flush();

        $this->addFlash('success', "L'activité a été supprimée avec succès!");

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/destination/new', name: 'app_admin_destination_new', methods: ['GET', 'POST'])]
    public function destinationNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $destination = new Destination();
        $form = $this->createForm(DestinationType::class, $destination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($destination);
            $entityManager->flush();

            $this->addFlash('success', 'La destination a été ajoutée avec succès!');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/form.html.twig', [
            'title' => 'Ajouter une destination',
            'form' => $form->createView(),
            'back_route' => 'app_admin',
        ]);
    }

    #[Route('/admin/destination/{id}/edit', name: 'app_admin_destination_edit', methods: ['GET', 'POST'])]
    public function destinationEdit(Request $request, Destination $destination, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DestinationType::class, $destination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La destination a été modifiée avec succès!');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/form.html.twig', [
            'title' => 'Modifier la destination',
            'form' => $form->createView(),
            'back_route' => 'app_admin',
        ]);
    }

    #[Route('/admin/destination/{id}/delete', name: 'app_admin_destination_delete', methods: ['POST'])]
    public function destinationDelete(Request $request, Destination $destination, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_destination_' . $destination->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_admin');
        }

        $entityManager->remove($destination);
        $entityManager->flush();

        $this->addFlash('success', 'La destination a été supprimée avec succès!');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/reservation/new', name: 'app_admin_reservation_new', methods: ['GET', 'POST'])]
    public function reservationNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été ajoutée avec succès!');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/form.html.twig', [
            'title' => 'Ajouter une réservation',
            'form' => $form->createView(),
            'back_route' => 'app_admin',
        ]);
    }

    #[Route('/admin/reservation/{id}/delete', name: 'app_admin_reservation_delete', methods: ['POST'])]
    public function reservationDelete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_reservation_' . $reservation->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_admin');
        }

        $entityManager->remove($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'La réservation a été supprimée avec succès!');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/contact/{id}/delete', name: 'app_admin_contact_delete', methods: ['POST'])]
    public function contactDelete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_contact_' . $contact->getId(), (string) $request->request->get('_token'))) {
            return $this->redirectToRoute('app_admin');
        }

        $entityManager->remove($contact);
        $entityManager->flush();

        $this->addFlash('success', 'Le message de contact a été supprimé avec succès!');

        return $this->redirectToRoute('app_admin');
    }
}
