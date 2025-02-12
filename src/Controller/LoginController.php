<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Flasher\Prime\FlasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class LoginController extends AbstractController
{


    private FlasherInterface $flasher;

        public function __construct(FlasherInterface $flasher)
    {
        $this->flasher = $flasher;
    }

    #[Route('/registration', name: 'app_registration')]
    public function registration(Request $request, EntityManagerInterface $manager,UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User();
        $form= $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $hash=$passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTimeImmutable()); 
            $manager->persist($user);
            //dd($user);
            $manager->flush();
            $this->addFlash('success' ,'Compte créé avec succès ');
            /*$email =(new Email())
              ->from('fotsoyvesdonald@gmail.com')
              ->to($user->getEmail())
              ->subject('Bienvenue sur le site')
              ->text("Merci pour votre Insciption {$user->getUserName()} ");
            $mailer->send($email);*/
            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/registration.html.twig', [
            'form' => $form->createView()
        ]);

    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        
        $error= $authenticationUtils->getLastAuthenticationError();
        $email=$authenticationUtils->getLastUsername();

        //notyf()->addError('Une erreur est survenue , votre email ou le mot de passe saisir est incorrect');
        return $this->render('login/index.html.twig', [
            'hasError'=> $error !==null,
            'email'=>  $email
        ]);
    }


    #[Route('/deconnexion', name: 'security_logout')]
    public function logout(){

    }

}
