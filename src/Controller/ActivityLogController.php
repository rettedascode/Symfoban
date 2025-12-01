<?php

namespace App\Controller;

use App\Repository\ActivityLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity')]
class ActivityLogController extends AbstractController
{
    #[Route('/', name: 'app_activity_log_index', methods: ['GET'])]
    public function index(
        ActivityLogRepository $activityLogRepository,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $entityType = $request->query->get('entityType');
        $entityId = $request->query->get('entityId');

        if ($entityType && $entityId) {
            $logs = $activityLogRepository->findByEntity($entityType, (int) $entityId);
        } else {
            $logs = $activityLogRepository->findRecent(100);
        }

        return $this->render('activity_log/index.html.twig', [
            'logs' => $logs,
            'entityType' => $entityType,
            'entityId' => $entityId,
        ]);
    }
}

