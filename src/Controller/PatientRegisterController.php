<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Patient;
use App\Form\PatientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PatientRegisterController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/patient_register', name: 'patient_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PatientType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $patient = $form->getData();

            $patient->setSlug($this->generateSlug($patient->getFirstName(), $patient->getLastName()));
            
            $hashedPassword = $this->passwordHasher->hashPassword($patient, $patient->getPassword());
            $patient->setPassword($hashedPassword);

            $patient->setRoles(['patient']);

            $is_followed = $patient->getIsFollowed();
            if ($is_followed === null || $is_followed === ''){$is_followed = false;}
            $patient->setIsFollowed($is_followed);

            $registrationDate = new DateTime('now', new DateTimeZone('Europe/Paris'));
            $patient->setRegistrationDate($registrationDate);

            echo '<pre>';
            var_dump($patient);
            echo '</pre>';
            
            $entityManager->persist($patient);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('patient_register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function generateSlug(string $firstName, string $lastName): string
    {
        $fullName = $firstName . ' ' . $lastName;

        //replace accents letters
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