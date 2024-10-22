<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')] // form is include in the header so we don't need route ?
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        // dd($emailService);


        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $token = Uuid::v4()->toRfc4122();

            $user->setToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            $confirmationLink = $this->generateUrl('app_confirm_email', [
                'token' => $token,
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $emailService->sendEmail(
                'julien.caussade@gmail.com',
                $user->getEmail(),
                'Please confirm your registration',
                'Thanks you for your registration.
                Please confirm you account by clicking on this link:
                <a href="' . $confirmationLink . '">Confirm my account</a>'
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form,
            'name' => 'Register',
        ]);
    }
}
