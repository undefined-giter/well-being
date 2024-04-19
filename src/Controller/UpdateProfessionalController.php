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
        $user = $this->getUser();

        if(!$user){return $this->redirectToRoute('login');}

        if(!in_array('professional', $user->getRoles())){
            $session->getFlashBag()->add('info', 'You are not registered as a professional.');
            return $this->redirectToRoute('personal_page', ['slug' => $user->getSlug()]);
        }

        $original_professional = $user;
        
        $original_professional_spe = json_encode($original_professional->getSpecialization());
        $original_professional_pic = $original_professional->getSpecialization();

        $form = $this->createForm(UpdateProfessionalType::class, $original_professional);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
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


            if ($form->get('delete_specialization')->getData()) {
                $professional->setSpecialization([]);
            }elseif (!$form->get('specialization')->getData() && !$form->get('other_specialization')->getData()) {
                $originalSpecialization = json_decode($form->get('hidden_original_specialization')->getData(), true);
                $professional->setSpecialization($originalSpecialization);
            }else{
                $specialization = $form->get('specialization')->getData();
                $personal_specialization = $form->get('other_specialization')->getData();
                if (!empty($personal_specialization)){$specialization[] = $personal_specialization;}
                $professional->setSpecialization($specialization);
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
            'hidden_original_specialization' => $original_professional_spe,
            'googleMapsApiKey' => $googleMapsApiKey,
        ]);
    }
}
