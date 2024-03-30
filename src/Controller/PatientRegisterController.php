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

            $patient->setFirstName($this->capitalizeName($patient->getFirstName()));

            $patient->setLastName($this->capitalizeName($patient->getLastName()));

            $patient->setSlug($this->generateSlug($patient->getFirstName(), $patient->getLastName()));
            
            $hashedPassword = $this->passwordHasher->hashPassword($patient, $patient->getPassword());
            $patient->setPassword($hashedPassword);

            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile instanceof UploadedFile) {
                $originalFileName = $pictureFile->getClientOriginalName();
                $uuid = Uuid::v4()->__toString();
                $extension = $pictureFile->getClientOriginalExtension();
                $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '_' . $uuid . '.' . $extension;
            }

            $patient->setRoles(['patient']);

            $is_followed = $patient->getIsFollowed();
            if ($is_followed === null || $is_followed === ''){$is_followed = false;}
            $patient->setIsFollowed($is_followed);

            $registrationDate = new DateTime('now', new DateTimeZone('Europe/Paris'));
            $patient->setRegistrationDate($registrationDate);
            
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


    private function capitalizeName($name) {
        $name = ucwords($name);
        $name = preg_replace_callback('/(?:\s|-|^)([a-z])/', function($matches) {
            return strtoupper($matches[0]);
        }, $name);
    
        return $name;
    }

    private function generateSlug(string $firstName, string $lastName): string
    {
        $fullName = $firstName . ' ' . $lastName;

        $slug = str_replace(
            ['à', 'â', 'ä', 'á', 'ã', 'å', 'î', 'ï', 'ì', 'í', 
            'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 'ù', 'û', 'ü', 'ú', 
            'é', 'è', 'ê', 'ë', 'ç', 'ÿ', 'ñ', ],
            ['a', 'a', 'a', 'a', 'a', 'a', 'i', 'i', 'i', 'i', 
            'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 
            'e', 'e', 'e', 'e', 'c', 'y', 'n', ],
            $fullName);
        
        $slug = str_replace(' ', '-', $slug);
        $slug = strtolower($slug);
        $slug = substr($slug, 0, 255);
        
        return $slug;
    }
}