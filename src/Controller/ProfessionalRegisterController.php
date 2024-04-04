<?php

namespace App\Controller;

use App\Entity\Professional;
use App\Form\ProfessionalType;
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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProfessionalRegisterController extends AbstractController
{
    private $passwordHasher;
    private $session;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/professional-register', name: 'professional_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, ParameterBagInterface $params): Response
    {
        if ($this->getUser() && in_array('professional', $this->getUser()->getRoles())) {
            $session->getFlashBag()->add('info', 'You are already registered as a professional.');
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(ProfessionalType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $professional = $form->getData();

            // var_dump($professional);die;
            $professional->setFirstName($professional->getFirstName());

            $professional->setLastName($professional->getLastName());

            $professional->setSlug($professional->generateSlug());
            
            $hashedPassword = $this->passwordHasher->hashPassword($professional, $professional->getPassword());
            $professional->setPassword($hashedPassword);

            $pictureFile = $professional->getPicture();
            if ($pictureFile instanceof UploadedFile) {
                $originalFileName = $pictureFile->getClientOriginalName();
                $uuid = Uuid::v4()->__toString();
                $extension = $pictureFile->getClientOriginalExtension();
                $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '_' . $uuid . '.' . $extension;
            }

            $specialization = $form->get('specialization')->getData();
            $personal_specialisation = $form->get('other_specialization')->getData();
            if (!empty($personal_specialisation)){$specialization[] = $personal_specialisation;}
            $professional->setSpecialization($specialization);

            $location = $professional->getLocation();
            $professional->setLocation($location);
            
            $session->getFlashBag()->add('success', [
                'message' => 'You have been recorded, please login🌳',
                'duration' => 5000
            ]);
            
            // $loc = $form->get('location')->getData();
            // echo '<pre>';
            // var_dump($loc);
            // echo '</pre>';
            // die;

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
