<?php

namespace App\Controller;

use App\Form\ModifierProfilType;
use App\Repository\ApplicationRepository;
use App\Repository\DemanderRoleRepository;
use App\Repository\RoleRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
public function index(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    $form = $this->createForm(ModifierProfilType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile|null $file */
        $file = $form->get('photo')->getData();

        if ($file) {
            $newFilename = uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('profile_photos_directory'), // Vérifie ce paramètre dans services.yaml
                $newFilename
            );
            $user->setPhoto($newFilename);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Votre profil a bien été mis à jour.');

        return $this->redirectToRoute('app_compte');
    }

    return $this->render('compte/index.html.twig', [
        'form' => $form->createView(),
    ]);
}
    #[Route('/compte/fil-actualite', name: 'app_compte_fil_actualite')]
    public function filActualite(
        RoleRepository $roleRepo,
        ApplicationRepository $appliRepo,
        DemanderRoleRepository $demandeRepo,
       
    ): Response {
        // On récupère les dernières données
        $dernieresApplications = $appliRepo->findBy([], ['id' => 'DESC'], 5);
        $derniersRoles = $roleRepo->findBy([], ['id' => 'DESC'], 5);
        $dernieresDemandes = $demandeRepo->findBy([], ['id' => 'DESC'], 5);
      
        return $this->render('compte/fil_actualite.html.twig', [
            'apps' => $dernieresApplications,
            'roles' => $derniersRoles,
            'demandes' => $dernieresDemandes,
            
        ]);
    }

}
