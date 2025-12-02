<?php

namespace App\Controller;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SSOController extends AbstractController
{
    #[Route('/sso/redirect/{id}', name: 'sso_redirect')]
    public function redirectToApp(Application $application): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_connexion');
        }

        // Clé secrète commune (même dans les mini applis)
        $secret = $_ENV['SSO_SECRET'];

        // Payload du JWT
        $payload = [
            'user' => [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'email' => $user->getEmail(),
            ],
            'exp' => time() + 300 // expire dans 5 minutes
        ];

        // Génération du token
        $token = JWT::encode($payload, $secret, 'HS256');

        // Redirection vers l’application
     
        $redirectUrl = rtrim($application->getUrl(), '/') . '/sso-login.html?token=' . urlencode($token);
        
       

        return $this->redirect($redirectUrl);
    }
}
