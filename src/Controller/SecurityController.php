<?php

namespace App\Controller;

use App\Entity\Patient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last email entered by the user
        $latestEmailRegistered = $this->getLastRegisteredEmail($entityManager);

        return $this->render('security/login.html.twig', ['latestEmailRegistered' => $latestEmailRegistered, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private function getLastRegisteredEmail(EntityManagerInterface $entityManager): ?string
    {
        $latestPatient = $entityManager->getRepository(Patient::class)->findOneBy([], ['id' => 'DESC']);

        return $latestPatient ? $latestPatient->getEmail() : null;
    }
}
