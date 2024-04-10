<?php

namespace App\Controller;

use AppEntityPatient;
use App\Entity\Patient;
use AppFormUpdatePatientType;
use Symfony\Component\Uid\Uuid;
use App\Form\UpdatePatientType;
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
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class UpdatePatientController extends AbstractController
{
    private $entityManager;
    private $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    #[Route("patient-update", name: "patient_update")]
    // #[IsGranted('patient')]
    public function index(Request $request, ParameterBagInterface $params): Response
    {
        if(!$this->getUser()){$this->redirectToRoute('homepage');}

        $user = $this->getUser();

        if(!in_array('patient', $user->getRoles())){
            $session->getFlashBag()->add('info', 'You are not registered as a patient.');
            return $this->redirectToRoute('personal_page', ['slug' => $user->getSlug()]);
        }

        $original_patient = $user;
        
        $form = $this->createForm(UpdatePatientType::class, $original_patient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $patient = $form->getData();

            $patient->setFirstName($patient->getFirstName());

            $patient->setLastName($patient->getLastName());

            $firstName = $patient->getFirstName();
            $lastName = $patient->getLastName();
            $slug = $this->slugger->slug($firstName . '-' . $lastName)->lower();
            $patient->setSlug($slug);
            
            if ($form->get('delete_picture')->getData()) {
                $patient->setPicture('');
            }elseif ($patient->getPicture() == NULL) {
                $patient->setPicture($form->get('hidden_original_picture')->getData());
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
                        $patient->setPicture($finalFileName);
                    }catch(FileException $e){
                        throw new \RuntimeException('Problem happened the upload of the profile picture: ' . $e->getMessage());
                    }
                }
            }

            if ($form->get('delete_interestedIn')->getData()) {
                $patient->setInterestedIn([]);
            }elseif (!$form->get('interestedIn')->getData()) {
                $originalInterests = json_decode($form->get('hidden_original_interests')->getData(), true);
                $patient->setInterestedIn($originalInterests);
            }else{
                $interestedIn = $form->get('interestedIn')->getData();
                $patient->setInterestedIn($interestedIn);
            }

            $this->entityManager->persist($patient);
            $this->entityManager->flush();

            return $this->redirectToRoute('personal_page', ['slug' => $this->getUser()->getSlug()]);
        }
        
        if ($form->isSubmitted()){
            $session->getFlashBag()->add('danger', 'Please, check your form.');
        }

        return $this->render('update_patient/index.html.twig', [
            'form' => $form->createView(),
            'profilePicture' => $original_patient->getPicture(),
            'originalInterests' => json_encode($original_patient->getInterestedIn()),
        ]);
    }
}
