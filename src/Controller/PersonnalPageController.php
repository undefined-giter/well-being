<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PersonnalPageController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/page-of-{slug}', name: 'personal_page')]
    public function index(string $slug, ParameterBagInterface $params): Response
    {
        $page_user = $this->entityManager->getRepository(User::class)->findOneBy(['slug' => $slug]);

        if (!$page_user) {
            return $this->redirectToRoute('homepage');
        }

        $googleMapsApiKey = $params->get('GOOGLE_MAPS_API_KEY');

        $user = $this->getUser();
        if (!$user || $user->getId() != $page_user->getId() ) { 
            return $this->render('page_of/index.html.twig', [
                'pu' => $page_user,
                'googleMapsApiKey' => $googleMapsApiKey,
            ]);
        }

        return $this->render('personnal_page/index.html.twig', [
            'pu' => $page_user,
        ]);
    }
}
