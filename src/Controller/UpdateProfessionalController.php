<?php

namespace App\Controller;

use AppEntityprofessional;
use App\Entity\professional;
use AppFormUpdateProfessionalType;
use Symfony\Component\Uid\Uuid;
use App\Form\UpdateProfessionalType;
use Doctrine\ORM\EntityManagerInterface;
use SymfonyComponentHttpFoundationRequest;
use SymfonyComponentHttpFoundationResponse;
use SymfonyComponentRoutingAnnotationRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use SymfonyBundleFrameworkBundleControllerAbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UpdateProfessionalController extends AbstractController
{
    private $entityManager;
    private $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    #[Route('/professional-update', name: 'professional_update')]
    public function index(SessionInterface $session, Request $request, ParameterBagInterface $params): Response
    {
        if(!$this->getUser()){$this->redirectToRoute('login');}

        $user = $this->getUser();

        if(!in_array('professional', $user->getRoles())){
            $session->getFlashBag()->add('info', 'You are not registered as a professional.');
            return $this->redirectToRoute('personal_page', ['slug' => $user->getSlug()]);
        }

        $original_professional = $user;
        
        $form = $this->createForm(UpdateProfessionalType::class, $original_professional);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $professional = $form->getData();

            $professional->setFirstName($professional->getFirstName());

            $professional->setLastName($professional->getLastName());

            $firstName = $professional->getFirstName();
            $lastName = $professional->getLastName();
            $slug = $this->slugger->slug($firstName . '-' . $lastName)->lower();
            $professional->setSlug($slug);
            
            if ($form->get('delete_picture')->getData()) {
                $professional->setPicture('');
            }elseif ($professional->getPicture() == NULL) {
                $professional->setPicture($form->get('hidden_original_picture')->getData());
            }else{
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
            }

            if ($form->get('delete_interestedIn')->getData()) {
                $professional->setInterestedIn([]);
            }elseif (!$form->get('interestedIn')->getData()) {
                $originalInterests = json_decode($form->get('hidden_original_interests')->getData(), true);
                $professional->setInterestedIn($originalInterests);
            }else{
                $interestedIn = $form->get('interestedIn')->getData();
                $professional->setInterestedIn($interestedIn);
            }

            $this->entityManager->persist($professional);
            $this->entityManager->flush();

            return $this->redirectToRoute('personal_page', ['slug' => $this->getUser()->getSlug()]);
        }
        
        if ($form->isSubmitted()){
            $session->getFlashBag()->add('danger', 'Please, check your form.');
        }

        $googleMapsApiKey = $params->get('GOOGLE_MAPS_API_KEY');

        return $this->render('update_professional/index.html.twig', [
            'form' => $form->createView(),
            'profilePicture' => $original_professional->getPicture(),
            'originalSpecialization' => json_encode($original_professional->getSpecialization()),
            'googleMapsApiKey' => $googleMapsApiKey,
        ]);
    }
}
