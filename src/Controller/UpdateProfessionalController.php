<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UpdateProfessionalController extends AbstractController
{
    #[Route('/professional-update', name: 'professional_update')]
    public function index(): Response
    {
        return $this->render('update_professional/index.html.twig', [
            'controller_name' => 'UpdateProfessionalController',
        ]);
    }
}
