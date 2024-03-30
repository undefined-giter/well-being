<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NavigationController extends AbstractController
{
    #[Route('', name: 'navigation')]
    public function index(): Response
    {
        return $this->render('navigation/index.html.twig', []);
    }
}
