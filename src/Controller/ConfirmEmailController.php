<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConfirmEmailController extends AbstractController
{
    #[Route('/confirm-email/{token}', name: 'app_confirm_email')]
    public function index(string $token, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $userRepository->findOneBy(['token' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('This confirmation token isn\'t valid');
        }

        $user->setToken(null);
        $user->setVerified(true);
        $entityManagerInterface->flush();

        return $this->render('confirm_email/index.html.twig', [
            'user' => $user,
            'name' => "Email confirmation",
        ]);
    }
}
