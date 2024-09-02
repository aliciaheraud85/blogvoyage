<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UsersAuthenticator $authenticator,UserAuthenticatorInterface $userAuthenticator, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $avatarFile = $form->get('avatar')->getData(); //récupère toutes les données de l'avatar
            if($avatarFile){
                $newFileName = uniqid() . '-'.$avatarFile->getExtension();  //uniqid = permet qu'il n'y ai pas deux avatars pareils, permet de changer le nom du fichier images également
                $avatarFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/divers/avatars', //donne le chemin pour aller mettre l'avatar dans le dossier avatars
                    $newFileName
                );
                //Mettre à jour l'avatar dans l'entité User avec le nouveau chemin
                $user->setAvatar($newFileName); //fait la connexion entre le nom de l'image de la base de données et le nom de l'avatar cryptée
            }
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

        

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
