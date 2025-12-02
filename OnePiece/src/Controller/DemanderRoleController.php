<?php

namespace App\Controller;

use App\Entity\DemanderRole;
use App\Entity\Role;
use App\Repository\DemanderRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DemanderRoleController extends AbstractController
{
    #[Route('/role', name: 'UtilisateurRole')]
    public function availableRoles(
        \App\Repository\RoleRepository $roleRepo,
        DemanderRoleRepository $demandeRepo
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $roles = $roleRepo->findAll();

        $approvedRoles = $demandeRepo->findBy([
            'utilisateur' => $user,
            'status' => DemanderRole::STATUS_APPROUVEE
        ]);

        return $this->render('role/UtilisateurRole.html.twig', [
            'roles' => $roles,
            'approvedRoles' => array_map(fn($dr) => $dr->getRole(), $approvedRoles),
        ]);
    }

    #[Route('/role/demande/{id}', name: 'demande_role')]
    public function requestRole(Role $role, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $existing = $em->getRepository(DemanderRole::class)->findOneBy([
            'utilisateur' => $user,
            'role' => $role,
            'status' => DemanderRole::STATUS_ATTENTE
        ]);

        if ($existing) {
            $this->addFlash('warning', 'Vous avez déjà une demande en cours pour ce rôle.');
        } else {
            $rq = new DemanderRole();
            $rq->setUser($user);
            $rq->setRole($role);

            $em->persist($rq);
            $em->flush();

            $this->addFlash('success', 'Votre demande a été enregistrée.');
        }

        return $this->redirectToRoute('UtilisateurRole');
    }

    #[Route('/role/demande/{id}/annuler', name: 'annuler_demande', methods: ['POST'])]
    public function cancelRequest(DemanderRole $demande, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user || $demande->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        if ($demande->getStatus() === DemanderRole::STATUS_ATTENTE) {
            $em->remove($demande);
            $em->flush();
            $this->addFlash('danger', 'Votre demande a été annulée.');
        }

        return $this->redirectToRoute('UtilisateurRoleDemande');
    }

    #[Route('/ma-demande', name: 'UtilisateurRoleDemande')]
    public function myRequests(DemanderRoleRepository $repo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $requests = $repo->findBy(['utilisateur' => $user], ['dateDemande' => 'DESC']);
        return $this->render('role/ma-demande.html.twig', [
            'requests' => $requests
        ]);
    }

    #[Route('/mes-applications', name: 'mes_applications')]
    public function mesApplications(DemanderRoleRepository $demandeRepo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $approvedDemandes = $demandeRepo->findBy([
            'utilisateur' => $user,
            'status' => DemanderRole::STATUS_APPROUVEE
        ]);

        $applications = [];

        foreach ($approvedDemandes as $demande) {
            $role = $demande->getRole();
            foreach ($role->getApplications() as $app) {
                $applications[$app->getId()] = $app;
            }
        }

        return $this->render('role/mes-applications.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route('/admin/RoleDemandee', name: 'admin_role_demandee')]
    public function listRequests(DemanderRoleRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $requests = $repo->findBy([], ['dateDemande' => 'DESC']);
        return $this->render('admin/role/Demande.html.twig', [
            'requests' => $requests
        ]);
    }

    #[Route('/admin/RoleDemandee/{id}/approuvee', name: 'RoleApprouvee', methods: ['POST'])]
    public function approve(DemanderRole $requestEntity, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($requestEntity->getStatus() === DemanderRole::STATUS_ATTENTE) {
            $requestEntity->setStatus(DemanderRole::STATUS_APPROUVEE);
            $em->flush();
            $this->addFlash('success', 'Demande approuvée.');
        }

        return $this->redirectToRoute('admin_role_demandee');
    }

    #[Route('/admin/RoleDemandee/{id}/rejetee', name: 'RoleRejetee', methods: ['POST'])]
    public function reject(DemanderRole $requestEntity, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($requestEntity->getStatus() === DemanderRole::STATUS_ATTENTE) {
            $requestEntity->setStatus(DemanderRole::STATUS_REJETEE);
            $em->flush();
            $this->addFlash('danger', 'Demande rejetée.');
        }

        return $this->redirectToRoute('admin_role_demandee');
    }
}
