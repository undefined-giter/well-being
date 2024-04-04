<?php

namespace App\Controller;

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


class PatientRegisterController extends AbstractController
{
    private $passwordHasher;
    private $session;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/patient-register', name: 'patient_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if ($this->getUser() && in_array('patient', $this->getUser()->getRoles())) {
            $session->getFlashBag()->add('info', 'You are already registered as a patient.');
            return $this->redirectToRoute('login');
        }
        
        $form = $this->createForm(PatientType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $patient = $form->getData();

            $patient->setFirstName($patient->getFirstName());

            $patient->setLastName($patient->getLastName());

            $patient->setSlug($patient->generateSlug());
            
            $hashedPassword = $this->passwordHasher->hashPassword($patient, $patient->getPassword());
            $patient->setPassword($hashedPassword);

            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile instanceof UploadedFile) {
                $originalFileName = $pictureFile->getClientOriginalName();
                $uuid = Uuid::v4()->__toString();
                $extension = $pictureFile->getClientOriginalExtension();
                $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '_' . $uuid . '.' . $extension;
            }

            $is_followed = $patient->getIsFollowed();
            if ($is_followed === null || $is_followed === ''){$is_followed = false;}
            $patient->setIsFollowed($is_followed);
            
            $session->getFlashBag()->add('success', [
                'message' => 'You have been recorded, please login🌳',
                'duration' => 5000
            ]);
            
            $entityManager->persist($patient);
            $entityManager->flush();

            $session->set('just_registered', true);
            
            return $this->redirectToRoute('login', ['latest_email_registered' => $patient->getEmail()]);
        }

        return $this->render('patient_register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}