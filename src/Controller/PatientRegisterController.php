<?php

namespace App\Controller;

use RuntimeException;
use App\Entity\Patient;
use App\Form\PatientType;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class PatientRegisterController extends AbstractController
{
    private $passwordHasher;
    private $session;
    private $slugger;

    public function __construct(UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger)
    {
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
    }

    #[Route('/patient-register', name: 'patient_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, ParameterBagInterface $params): Response
    {
        if ($this->getUser()) {
            if(in_array('patient', $this->getUser()->getRoles())){
                $session->getFlashBag()->add('info', 'You are already registered as a patient.');
            }
            if(in_array('professional', $this->getUser()->getRoles())){
                $session->getFlashBag()->add('info', 'You are registered as a patient and can\'t register as a professional.');
            }
            return $this->redirectToRoute('login');
        }
        
        $form = $this->createForm(PatientType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $patient = $form->getData();

            // Easier to reapeat some code on 3 controllers than making a service
            $patient->setFirstName($patient->getFirstName());

            $patient->setLastName($patient->getLastName());

            $firstName = $patient->getFirstName();
            $lastName = $patient->getLastName();
            $slug = $this->slugger->slug($firstName . '-' . $lastName)->lower();
            $patient->setSlug($slug);
            
            $hashedPassword = $this->passwordHasher->hashPassword($patient, $patient->getPassword());
            $patient->setPassword($hashedPassword);

            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $originalFileName = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $slugifiedFileName = $this->slugger->slug($originalFileName);
                $uuid = Uuid::v4()->__toString();
                $extension = $pictureFile->getClientOriginalExtension();
                $finalFileName = $slugifiedFileName . '_' . $uuid . '.' . $extension;
    
                try{
                    $pictureFile->move(
                        $params->get('pictureProfile_directory'),
                        $finalFileName
                    );
                    $patient->setPicture($finalFileName);
                }catch(FileException $e){
                    throw new \RuntimeException('Problem happened the upload of the profile picture: ' . $e->getMessage());
                }
            }
            // end of repeated code -> ProfessionalRegisterController -> UpdatedPatientController

            $interestedIn = $form->get('interestedIn')->getData();
            $patient->setInterestedIn($interestedIn);

            $is_followed = $patient->getIsFollowed();
            if ($is_followed === null || $is_followed === ''){$is_followed = false;}
            $patient->setIsFollowed($is_followed);
            
            $session->getFlashBag()->add('success', [
                'message' => 'You have been recorded, please login🌳',
                'duration' => 5000
            ]);
            $description = $form['description']->getData();
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