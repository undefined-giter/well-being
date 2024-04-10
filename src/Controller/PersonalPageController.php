<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PersonalPageController extends AbstractController
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
            return $this->redirectToRoute('homepage', ['error' => 'There is no such user.']);
        }

        $user = $this->getUser();
        $latitude = null;
        $longitude = null;
        $address = null;
        if (!$user || $user->getId() != $page_user->getId() ) {
            if(in_array('professional', $page_user->getRoles())){
                if($page_user->getLocation()){
                    $locationData = json_decode($page_user->getLocation(), true);
                    foreach ($locationData as $item) {
                        if ($item['name'] === 'Latitude') {
                            $latitude = $item['value'];
                        } elseif ($item['name'] === 'Longitude') {
                            $longitude = $item['value'];
                        } elseif ($item['name'] === 'Address') {
                            $address = $item['value'];
                        }
                    }
                }
            }

            $googleMapsApiKey = $params->get('GOOGLE_MAPS_API_KEY');
            return $this->render('page_of/index.html.twig', [
                'pu' => $page_user,
                'googleMapsApiKey' => $googleMapsApiKey,
                'address' => $address,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
        }

        return $this->render('personal_page/index.html.twig', [
            'pu' => $page_user,
        ]);
    }
}
