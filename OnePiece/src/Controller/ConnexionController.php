<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion')]
    public function index(AuthenticationUtils $authentication, Request $request,HttpClientInterface $httpClient ): Response
    {
        if ($request->isMethod('POST')) {
            $recaptchaResponse = $request->request->get('g-recaptcha-response');

            if (!$recaptchaResponse) {
                $this->addFlash('error', 'Veuillez valider le reCAPTCHA.');
                return $this->redirectToRoute('app_connexion');
            }

            // ✅ Envoie la requête de vérification à Google
            $response = $httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe', // 🛑 Mets ta vraie clé ici (sans espace à la fin)
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->getClientIp(),
                ]
            ]);

            $data = $response->toArray();

            if (!$data['success']) {
                $this->addFlash('error', 'Échec de la vérification reCAPTCHA.');
                return $this->redirectToRoute('app_connexion');
            }

            // ✅ Le reCAPTCHA est valide → Symfony va traiter l'authentification normalement
        }

        $PieceId=$authentication->getLastUsername();
        $error=$authentication->getLastAuthenticationError();
        return $this->render('connexion/index.html.twig', [
            'error' => $error,
            'last_username' => $PieceId,
        ]);
    }
     #[Route('/déconnexion', name: 'app_deconnexion', methods:['GET'])]
    public function logout(): never
    {
        throw new Exception(message:'Don\'t forget to activate logout in security.yaml');
    }
}
