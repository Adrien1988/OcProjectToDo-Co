<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{


    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, ['is_creation' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('password')->getData())
            );

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte a bien été créé.');

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', ['registration' => $form->createView()]);
    }


}
