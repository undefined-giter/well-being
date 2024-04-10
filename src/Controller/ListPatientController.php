<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListPatientController extends AbstractController
{
    #[Route('/list-patient', name: 'list_patient')]
    public function index(): Response
    {
        return $this->render('list_patient/index.html.twig', [
            'controller_name' => 'ListPatientController',
        ]);
    }
}
