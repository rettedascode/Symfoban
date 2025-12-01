<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\ColumnRepository;
use App\Service\ActivityLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tasks')]
class TaskController extends AbstractController
{
    #[Route('/reorder', name: 'app_task_reorder', methods: ['POST'])]
    public function reorder(
        Request $request,
        TaskRepository $taskRepository,
        ColumnRepository $columnRepository,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        ActivityLogService $activityLogService
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');

        try {
            $content = $request->getContent();
            if (empty($content)) {
                return new JsonResponse(['success' => false, 'error' => 'Empty request body'], Response::HTTP_BAD_REQUEST);
            }

            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['success' => false, 'error' => 'Invalid JSON: ' . json_last_error_msg()], Response::HTTP_BAD_REQUEST);
            }

            $csrfToken = $data['_token'] ?? null;
            if (!$this->isCsrfTokenValid('tasks_reorder', $csrfToken)) {
                return new JsonResponse(['success' => false, 'error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
            }

            $taskId = $data['taskId'] ?? null;
            $columnId = $data['columnId'] ?? null;
            $newPosition = $data['position'] ?? null;

            if ($taskId === null || $columnId === null || $newPosition === null) {
                return new JsonResponse(['success' => false, 'error' => 'Missing parameters'], Response::HTTP_BAD_REQUEST);
            }

            /** @var Task|null $task */
            $task = $taskRepository->find($taskId);
            $newColumn = $columnRepository->find($columnId);

            if (!$task || !$newColumn) {
                return new JsonResponse(['success' => false, 'error' => 'Task or column not found'], Response::HTTP_NOT_FOUND);
            }

            $oldColumn = $task->getColumn();
            $newPosition = max(0, (int) $newPosition);

            // Reorder tasks in old and new columns
            if ($oldColumn === $newColumn) {
                // Move within the same column
                $tasks = $oldColumn->getTasks()->toArray();
                usort($tasks, static fn (Task $a, Task $b) => $a->getPosition() <=> $b->getPosition());

                // Remove the task from the list
                $tasks = array_values(array_filter($tasks, static fn (Task $t) => $t->getId() !== $task->getId()));

                // Clamp position to list bounds
                $newPosition = min($newPosition, count($tasks));

                array_splice($tasks, $newPosition, 0, [$task]);

                foreach ($tasks as $index => $t) {
                    $t->setPosition($index);
                }
            } else {
                // Moving to a different column
                if ($oldColumn) {
                    $oldTasks = $oldColumn->getTasks()->toArray();
                    usort($oldTasks, static fn (Task $a, Task $b) => $a->getPosition() <=> $b->getPosition());

                    $oldTasks = array_values(array_filter($oldTasks, static fn (Task $t) => $t->getId() !== $task->getId()));

                    foreach ($oldTasks as $index => $t) {
                        $t->setPosition($index);
                    }
                }

                $task->setColumn($newColumn);

                $newTasks = $newColumn->getTasks()->toArray();
                usort($newTasks, static fn (Task $a, Task $b) => $a->getPosition() <=> $b->getPosition());

                $newPosition = min($newPosition, count($newTasks));

                array_splice($newTasks, $newPosition, 0, [$task]);

                foreach ($newTasks as $index => $t) {
                    $t->setPosition($index);
                }
            }

            $entityManager->flush();

            // Log activity
            $oldColumnName = $oldColumn ? $oldColumn->getName() : 'Unknown';
            $newColumnName = $newColumn->getName();
            $description = $oldColumn === $newColumn
                ? "Moved task '{$task->getTitle()}' to position {$newPosition} in column '{$newColumnName}'"
                : "Moved task '{$task->getTitle()}' from '{$oldColumnName}' to '{$newColumnName}' at position {$newPosition}";
            
            $activityLogService->log(
                'task_moved',
                'Task',
                $task->getId(),
                $description,
                $this->getUser()
            );

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ActivityLogService $activityLogService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assign the current user as the task creator
            $task->setCreatedBy($this->getUser());
            $entityManager->persist($task);
            $entityManager->flush();

            // Log activity
            $activityLogService->log(
                'task_created',
                'Task',
                $task->getId(),
                "Created task '{$task->getTitle()}' in column '{$task->getColumn()->getName()}'",
                $this->getUser()
            );

            return $this->redirectToRoute('app_board_show', ['id' => $task->getColumn()->getBoard()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Task $task,
        EntityManagerInterface $entityManager,
        ActivityLogService $activityLogService
    ): Response {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Log activity
            $activityLogService->log(
                'task_updated',
                'Task',
                $task->getId(),
                "Updated task '{$task->getTitle()}'",
                $this->getUser()
            );

            return $this->redirectToRoute('app_board_show', ['id' => $task->getColumn()->getBoard()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Task $task,
        EntityManagerInterface $entityManager,
        ActivityLogService $activityLogService
    ): Response {
        $boardId = $task->getColumn()->getBoard()->getId();
        $taskTitle = $task->getTitle();
        
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->get('_token'))) {
            // Log activity before deletion
            $activityLogService->log(
                'task_deleted',
                'Task',
                $task->getId(),
                "Deleted task '{$taskTitle}'",
                $this->getUser()
            );

            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_board_show', ['id' => $boardId], Response::HTTP_SEE_OTHER);
    }
}

