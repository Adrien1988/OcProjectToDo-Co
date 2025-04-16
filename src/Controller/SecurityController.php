<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request)
    {

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    // Normalement pas nécessaire, Symfony le gère pour toi, mais tu peux l'ajouter manuellement si nécessaire
    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheck()
    {
        // Cette méthode ne sera jamais appelée car Symfony la gère pour nous
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        // Symfony intercepte cette route automatiquement via security.yaml.
        throw new \Exception('Ne devrait jamais être atteint : Symfony intercepte cette route.');
    }
}
