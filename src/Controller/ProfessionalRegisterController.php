<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
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
            $professional->setFirstName($this->capitalizeName($professional->getFirstName()));

            $professional->setLastName($this->capitalizeName($professional->getLastName()));

            $professional->setSlug($this->generateSlug($professional->getFirstName(), $professional->getLastName()));
            
            $hashedPassword = $this->passwordHasher->hashPassword($professional, $professional->getPassword());
            $professional->setPassword($hashedPassword);

            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile instanceof UploadedFile) {
                $originalFileName = $pictureFile->getClientOriginalName();
                $uuid = Uuid::v4()->__toString();
                $extension = $pictureFile->getClientOriginalExtension();
                $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '_' . $uuid . '.' . $extension;
            }

            $professional->addRoles(['professional']);

            $is_followed = $professional->getIsFollowed();
            if ($is_followed === null || $is_followed === ''){$is_followed = false;}
            $professional->setIsFollowed($is_followed);

            $registrationDate = new DateTime('now', new DateTimeZone('Europe/Paris'));
            $professional->setRegistrationDate($registrationDate);
            
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
