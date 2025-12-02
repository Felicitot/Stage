<?php

namespace App\Controller\Admin;

use App\Repository\ApplicationRepository;
use App\Repository\RoleRepository;
use App\Repository\DemanderRoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(
        RoleRepository $roleRepo,
        DemanderRoleRepository $demandeRepo,
        ApplicationRepository $appRepo
    ): Response {
        // 1️⃣ Nombre total de rôles
        $totalRoles = $roleRepo->count([]);

        // 2️⃣ Nombre de demandes en attente (si tu as un champ "status")
        $pendingRequests = $demandeRepo->count(['status' => 'attente']);

        // 3️⃣ Applications les plus utilisées
        $qb = $appRepo->createQueryBuilder('a')
            ->select('a.nomAppli as nom, COUNT(r.id) as total')
            ->leftJoin('a.appliRole', 'r')
            ->groupBy('a.id')
            ->orderBy('total', 'DESC')
            ->setMaxResults(5);

        $appsData = $qb->getQuery()->getResult();

        // On prépare les données pour Chart.js
        $appNames = array_column($appsData, 'nom');
        $roleCounts = array_column($appsData, 'total');

        return $this->render('admin/dashboard/index.html.twig', [
            'totalRoles' => $totalRoles,
            'pendingRequests' => $pendingRequests,
            'appNames' => json_encode($appNames),
            'roleCounts' => json_encode($roleCounts),
        ]);
    }
}
