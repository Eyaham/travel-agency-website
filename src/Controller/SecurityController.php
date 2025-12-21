<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController

{
    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        return new Response('Test OK');
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
   public function register(
    Request $request,
    UserPasswordHasherInterface $hasher,
    EntityManagerInterface $em,
    TokenStorageInterface $tokenStorage
): Response {
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $plainPassword = $form->get('password')->getData();
        $user->setPassword($hasher->hashPassword($user, $plainPassword));

        $em->persist($user);
        $em->flush();

        // ⚡ Forcer la déconnexion après register
        $tokenStorage->setToken(null);

        $this->addFlash('success', 'Compte créé avec succès. Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('app_login');
    }

    return $this->render('security/register.html.twig', [
        'registerForm' => $form->createView(),
        'registrationForm' => $form->createView(),
        'form' => $form->createView(),
    ]);
}

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
public function login(AuthenticationUtils $authenticationUtils): Response
{
    // Si l'utilisateur est déjà connecté → home
    if ($this->getUser()) {
        return $this->redirectToRoute('app_home');
    }

    return $this->render('security/login.html.twig', [
        'last_username' => $authenticationUtils->getLastUsername(),
        'error' => $authenticationUtils->getLastAuthenticationError(),
    ]);
}

#[Route('/logout', name: 'app_logout')]
public function logout(): void
{
    throw new \LogicException('Symfony gère la déconnexion.');
}
}
