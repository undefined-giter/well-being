<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdatePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdatePasswordController extends AbstractController
{
    #[Route('/update-password', name: 'update_password')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        if(!$user){return $this->redirectToRoute('login');}

        $form = $this->createForm(UpdatePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Password updated successfully!');

            $slug = $user->getSlug();

            return $this->redirectToRoute('personal_page', [
                'slug' => $slug,
            ]);
        }

        return $this->render('update_password/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
