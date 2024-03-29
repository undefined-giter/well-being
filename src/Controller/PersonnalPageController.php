<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonnalPageController extends AbstractController
{
    #[Route('/personnal-page/{slug}', name: 'personnal_page')]
    public function index(): Response
    {
        return $this->render('personnal_page/index.html.twig', []);
    }
}
