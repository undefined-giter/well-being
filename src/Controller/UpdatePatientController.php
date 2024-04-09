<?php

namespace App\Controller;

use AppEntityPatient;
use AppFormUpdatePatientType;
use SymfonyComponentHttpFoundationRequest;
use SymfonyComponentHttpFoundationResponse;
use SymfonyComponentRoutingAnnotationRoute;
use SymfonyBundleFrameworkBundleControllerAbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UpdatePatientController extends AbstractController
{
    #[Route("patient-update", name: "patient_update")]
    #[IsGranted('patient')]
    public function patientUpdate(Request $request): Response
    {
        $patient = $this-getUser();

        $form = $this-createForm(UpdatePatientTypeclass, $patient);

        $form-handleRequest($request);
        if ($form-isSubmitted() && $form-isValid()) {
            $entityManager-persist($patient);
            $entityManager-flush();

            return $this-redirectToRoute('personnal_page');
        }

        return $this-render('patient_register/index.html.twig', [
            'form' => $form-createView()
        ]);
    }
}
