<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            // $email = new Email();

            // $email->from('boussaid.ho@gmail.com') // expediteur
            //         ->to($user->getEmail()) // le destinataire
            //         ->subject('insription agence loc') // le sujet
            //         ->html('<p>Merci de vous être inscrit sur notre site</p><br><p>n\'attendez plus pour reserver votre voiture....</p>'); // le message

            // $mailer->send($email); // envoie de l'email        

            $this->addFlash('success', "Inscription validé ! vous pouvez à présent vous connécter ci dessous.");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
