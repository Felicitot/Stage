<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Twilio\Rest\Client;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class MotDEPasseOublieController extends AbstractController
{
    
    #[Route('/mot-de-passe/verifier-identite', name: 'app_verif_identite')]
    public function verifyIdentity(Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager
): Response {

    if ($request->isMethod('POST')) {

        $submittedToken = $request->request->get('_csrf_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('verif_identite', $submittedToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $piece = $request->request->get('PieceId');
        $email = $request->request->get('email');
        $numTel = $request->request->get('numTel');
        $dateNaissance = $request->request->get('dateNaissance');
        $dateDelivrance = $request->request->get('dateDelivrance');

        $user = $em->getRepository(Utilisateur::class)->findOneBy(['PieceId' => $piece]);

        if (!$user) {
            $this->addFlash('error', "Matricule incorrect.");
            return $this->redirectToRoute('app_verif_identite');
        }

        if (
            $user->getEmail() !== $email ||
            $user->getNumTel() !== $numTel ||
            $user->getDateNaissance()->format('Y-m-d') !== $dateNaissance ||
            $user->getPieceId() !== $piece ||
            $user->getFaitLe()->format('Y-m-d') !== $dateDelivrance
        ) {
            $this->addFlash('error', "Les informations ne correspondent pas.");
            return $this->redirectToRoute('app_verif_identite');
        }

        // identite valide
        return $this->redirectToRoute('app_reset_password', [
            'piece' => $piece
        ]);
    }

    return $this->render('reset_sms/verify_identity.html.twig', [
        'csrf_token' => $csrfTokenManager->getToken('verif_identite')->getValue(),
    ]);
}
#[Route('/mot-de-passe/nouveau', name: 'app_reset_password')]
public function resetPassword(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher
): Response {

    $piece = $request->query->get('piece');

    $user = $em->getRepository(Utilisateur::class)->findOneBy(['PieceId' => $piece]);

    if (!$user) {
        $this->addFlash('error', 'Utilisateur introuvable.');
        return $this->redirectToRoute('app_verif_identite');
    }

    if ($request->isMethod('POST')) {

        $newPassword = $request->request->get('MotDePasse');

        $hashed = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashed);
        $em->flush();

        $this->addFlash('success', "Mot de passe réinitialisé avec succès !");
        return $this->redirectToRoute('app_connexion');
    }

    return $this->render('reset_sms/new_password.html.twig', [
        'piece' => $piece
    ]);
}


}

