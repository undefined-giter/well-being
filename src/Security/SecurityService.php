<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityService
{
    private $router;
    private $session;

    public function __construct(RouterInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    public function redirectToHomepageIfLoggedIn()
    {
        // Vérifier si l'utilisateur est connecté
        if ($this->session->get('user')) {
            // Rediriger l'utilisateur vers la page d'accueil
            return new RedirectResponse($this->router->generate('homepage'));
        }

        // L'utilisateur n'est pas connecté, ne pas rediriger
        return null;
    }
}