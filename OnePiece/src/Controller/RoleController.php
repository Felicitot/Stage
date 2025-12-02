<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleUserType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RoleController extends AbstractController
{
    #[Route('/role', name: 'app_role')]
    public function index(Request $request, EntityManagerInterface $entityManager,RoleRepository $roleRepo): Response
    {
        $role = new Role();
        $Roleform = $this->createForm(RoleUserType::class, $role);
        $Roleform->handleRequest($request);
        if($Roleform->isSubmitted() && $Roleform->isValid()){
            $entityManager->persist($role);
            $entityManager->flush(); 
            $this->addFlash(
                'success',
                "Ajout de rôle effectué"
            );
        }
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $roles = $roleRepo->createQueryBuilder('r')
                ->where('r.role LIKE :term OR r.description LIKE :term')
                ->setParameter('term', '%' . $searchTerm . '%')
                ->getQuery()
                ->getResult();
        } else {
            $roles = $roleRepo->findAll();
        }

       
        return $this->render('role/index.html.twig', [
            'RoleForm' => $Roleform->createView(),
            'roles' => $roles,
            'searchTerm' => $searchTerm
        ]);
    }
    #[Route('/role/edit/{id}', name: 'app_role_edit')]
    public function edit(Request $request, Role $role, EntityManagerInterface $em) {
        $form = $this->createForm(RoleUserType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Rôle modifié avec succès.');
            return $this->redirectToRoute('app_role');
        }

        return $this->render('role/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

#[Route('/role/delete/{id}', name: 'app_role_delete')]
    public function delete(Role $role, EntityManagerInterface $em) {
        $em->remove($role);
        $em->flush();
        $this->addFlash('danger', 'Rôle supprimé avec succès.');
        return $this->redirectToRoute('app_role');
    }

}
