<?php

namespace App\Controller;

use App\Form\ProfessionalType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfessionalRegisterController extends AbstractController
{
    #[Route('/professional_register', name: 'professional_register')]
    public function index(): Response
    {
        $form = $this->createForm(ProfessionalType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form->getData());
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('professional_register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
