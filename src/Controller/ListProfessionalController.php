<?php

namespace App\Controller;

use App\Repository\ProfessionalRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ListProfessionalController extends AbstractController
{
    private $ProfessionalRepository;

    public function __construct(ProfessionalRepository $ProfessionalRepository)
    {
        $this->ProfessionalRepository = $ProfessionalRepository;
    }

    #[Route('/list-professional', name: 'list_professional')]
    public function index(Request $request): Response
    {        
        if($this->getUser()){$isRegistered = true;}
        else{$isRegistered = false;}

        $specialisation = $request->query->get('specialisationFilter');

        $professionalSummary = $this->ProfessionalRepository->findProfessionalSummary($specialisation);

        return $this->render('list_professional/index.html.twig', [
            'isRegistered' => $isRegistered,
            'professionalSummary' => $professionalSummary,
        ]);
    }
}
