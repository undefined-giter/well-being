<?php

namespace App\Controller;

use App\Repository\PatientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ListPatientController extends AbstractController
{
    private $patientRepository;

    public function __construct(PatientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }
    
    #[Route('/list-patient', name: 'list_patient')]
    public function index(Request $request, SessionInterface $session): Response
    {
        if(!$this->getUser() || !in_array('professional', $this->getUser()->getRoles())){
            $session->getFlashBag()->add('info', 'The page you tried to access is reserved to professionals persons only.');
            return $this->redirectToRoute('login');
        }

        $interestFilter = $request->query->get('interestFilter');

        $freePatientsSummary = $this->patientRepository->findFreePatientSummary($interestFilter);

        return $this->render('list_patient/index.html.twig', [
            'freePatientsSummary' => $freePatientsSummary,
        ]);
    }
}
