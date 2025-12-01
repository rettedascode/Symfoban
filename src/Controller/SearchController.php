<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\BoardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/search')]
class SearchController extends AbstractController
{
    #[Route('/', name: 'app_search', methods: ['GET'])]
    public function search(
        Request $request,
        TaskRepository $taskRepository,
        BoardRepository $boardRepository
    ): Response {
        $query = $request->query->get('q', '');
        $tasks = [];
        $boards = [];

        if (!empty($query)) {
            $tasks = $taskRepository->search($query);
            $boards = $boardRepository->search($query);
        }

        return $this->render('search/index.html.twig', [
            'query' => $query,
            'tasks' => $tasks,
            'boards' => $boards,
        ]);
    }
}

