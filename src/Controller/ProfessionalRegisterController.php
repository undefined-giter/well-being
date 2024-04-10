<?php

namespace App\Controller;

use App\Entity\Professional;
use App\Form\ProfessionalType;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProfessionalRegisterController extends AbstractController
{
    private $passwordHasher;
    private $session;

    public function __construct(UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger)
    {
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
    }

    #[Route('/professional-register', name: 'professional_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, SluggerInterface $slugger, ParameterBagInterface $params): Response
    {
        if ($this->getUser()) {
            if(in_array('professional', $this->getUser()->getRoles())){
                $session->getFlashBag()->add('info', 'You are already registered as a professional.');
            }
            if(in_array('patient', $this->getUser()->getRoles())){
                $session->getFlashBag()->add('info', 'You are registered as a patient and can\'t register as a professional.');
            }
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(ProfessionalType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $professional = $form->getData();

            $professional->setFirstName($professional->getFirstName());

            $professional->setLastName($professional->getLastName());

            $firstName = $professional->getFirstName();
            $lastName = $professional->getLastName();
            $slug = $this->slugger->slug($firstName . '-' . $lastName)->lower();
            $professional->setSlug($slug);
            
            $hashedPassword = $this->passwordHasher->hashPassword($professional, $professional->getPassword());
            $professional->setPassword($hashedPassword);

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
                    $professional->setPicture($finalFileName);
                }catch(FileException $e){
                    throw new \RuntimeException('Problem happened the upload of the profile picture: ' . $e->getMessage());
                }
            }

            $specialization = $form->get('specialization')->getData();
            $personal_specialisation = $form->get('other_specialization')->getData();
            if (!empty($personal_specialisation)){$specialization[] = $personal_specialisation;}
            $professional->setSpecialization($specialization);

            $location = $form->get('location')->getData();
            $professional->setLocation($location);
            
            $session->getFlashBag()->add('success', [
                'message' => 'You have been recorded, please login🌳',
                'duration' => 5000
            ]);
            
            
            $entityManager->persist($professional);
            $entityManager->flush();

            $session->set('just_registered', true);
            
            return $this->redirectToRoute('login', ['latest_email_registered' => $professional->getEmail()]);
        }
        
        $googleMapsApiKey = $params->get('GOOGLE_MAPS_API_KEY');

        return $this->render('professional_register/index.html.twig', [
            'form' => $form->createView(),
            'googleMapsApiKey' => $googleMapsApiKey,
        ]);
    }
}
