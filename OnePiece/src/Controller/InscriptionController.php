<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\InscriptionUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


final class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, HttpClientInterface $httpClient, SluggerInterface $slugger, MailerInterface $mailer): Response
    {
           if ($request->isMethod('POST')) {
            $recaptchaResponse = $request->request->get('g-recaptcha-response');

            if (!$recaptchaResponse) {
                $this->addFlash('error', 'Veuillez valider le reCAPTCHA.');
                return $this->redirectToRoute('app_connexion');
            }

          
            $response = $httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe', 
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->getClientIp(),
                ]
            ]);

            $data = $response->toArray();

            if (!$data['success']) {
                $this->addFlash('error', 'Échec de la vérification reCAPTCHA.');
                return $this->redirectToRoute('app_connexion');
            }

            
        }
      
        $utilisateur = new Utilisateur();
        $form = $this->createForm(InscriptionUtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
             $rectoFile = $form->get('Recto')->getData();
             $versoFile = $form->get('Verso')->getData();

           

    // 📂 Dossier de destination
    $uploadsDirectory = $this->getParameter('uploads_directory');

        if ($rectoFile) {
            $originalFilename = pathinfo($rectoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$rectoFile->guessExtension();

            try {
                $rectoFile->move($uploadsDirectory, $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement du fichier recto.');
                return $this->redirectToRoute('app_inscription');
            }

            // 💾 Enregistrement du nom du fichier dans l’entité
            $utilisateur->setRecto($newFilename);
        }

        if ($versoFile) {
            $originalFilename = pathinfo($versoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$versoFile->guessExtension();

            try {
                $versoFile->move($uploadsDirectory, $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement du fichier verso.');
                return $this->redirectToRoute('app_inscription');
            }

            $utilisateur->setVerso($newFilename);
        }
                   $photoFile = $form->get('photo')->getData();
        if ($photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

            try {
                $photoFile->move(
                    $this->getParameter('profile_photos_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer l’erreur (par ex. message flash)
            }

            $utilisateur->setPhoto($newFilename);
           


        }
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($utilisateur, $plainPassword);
                $utilisateur->setPassword($hashedPassword);
            }
            
            $entityManager->persist($utilisateur);
            $entityManager->flush();
           
            $this->addFlash(
                'success',
                "Incription effectuéé,veuillez vous connecter"
            );
            // $mailer est injecté comme argument de la méthode
           $email = (new TemplatedEmail())
            ->from('ravoniainafelicitot@gmail.com')
            ->to($utilisateur->getEmail())
            ->subject('Confirmation de création de compte')
            ->htmlTemplate('emails/confirmation.html.twig')
            ->context([
                'user' => $utilisateur
            ]);


            try {
                $mailer->send($email);
            } catch (\Throwable $e) {
               dd('Erreur email : ' . $e->getMessage());
            }

        return $this->redirectToRoute('app_connexion');

        }
        return $this->render('inscription/index.html.twig', [
            'InscriptionForm' => $form->createView(),
        ]);
       
    }
}

