<?php

namespace App\Controller;

use App\Form\ProfessionalType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfessionalRegisterController extends AbstractController
{
    #[Route('/professional-register', name: 'professional_register')]
    public function index(): Response
    {
        $form = $this->createForm(ProfessionalType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form->getData());
            $entityManager->flush();

            $session->set('just_registered', true);

            return $this->redirectToRoute('login', ['latest_email_registered' => $patient->getEmail()]);
        }

        return $this->render('professional_register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
