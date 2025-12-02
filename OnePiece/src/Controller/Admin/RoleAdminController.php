<?php

namespace App\Controller\Admin;

use App\Entity\DemanderRole;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Role;
use App\Form\RoleUserType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;



#[Route('/admin/role', name: 'admin_roles_')]
class RoleAdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RoleRepository $roleRepo): Response
    {
        $roles = $roleRepo->findAll();
        return $this->render('admin/role/index.html.twig', [
            'roles' => $roles,
        ]);
    }

    #[Route('/creation', name: 'creation')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleUserType::class, $role);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
               foreach ($role->getApplications() as $application) {
                    $application->addAppliRole($role);
                }
            $em->persist($role);
            $em->flush();
            $this->addFlash('success', 'Rôle créé avec succès.');
            return $this->redirectToRoute('admin_roles_index');
        }
        return $this->render('admin/role/creation.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Role $role, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RoleUserType::class, $role);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($role->getApplications() as $application) {
                    $application->addAppliRole($role);
            }
            $em->flush();
            $this->addFlash('success', 'Rôle modifié avec succès.');
            return $this->redirectToRoute('admin_roles_index');
        }
        return $this->render('admin/role/edit.html.twig', [
            'form' => $form->createView(),
            'role' => $role
        ]);
    }

    #[Route('/suppression/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Role $role, Request $request, EntityManagerInterface $em): Response
    {
        // ✅ Vérifie le token CSRF pour la sécurité
        if (!$this->isCsrfTokenValid('delete' . $role->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('admin_roles_index');
        }

        // ✅ Supprime d'abord les demandes de rôle associées
        $demandes = $em->getRepository(DemanderRole::class)->findBy(['role' => $role]);
        foreach ($demandes as $demande) {
            $em->remove($demande);
        }

        // ✅ Ensuite, supprime le rôle
        $em->remove($role);
        $em->flush();

        $this->addFlash('danger', 'Rôle et demandes associées supprimés avec succès.');

        return $this->redirectToRoute('admin_roles_index');
    }
}
