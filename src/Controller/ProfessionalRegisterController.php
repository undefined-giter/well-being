<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Patient;
use App\Form\PatientType;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfessionalRegisterController extends AbstractController
{
    private $passwordHasher;
    private $session;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/professional-register', name: 'professional_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if ($this->getUser() && in_array('professional', $this->getUser()->getRoles())) {
            $session->getFlashBag()->add('info', 'You are already registered as a professional.');
            return $this->redirectToRoute('login');
        }

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
