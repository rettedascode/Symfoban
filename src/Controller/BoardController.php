<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\BoardTemplate;
use App\Entity\Column;
use App\Form\BoardType;
use App\Repository\BoardRepository;
use App\Repository\BoardTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/board')]
class BoardController extends AbstractController
{
    #[Route('/', name: 'app_board_index', methods: ['GET'])]
    public function index(BoardRepository $boardRepository): Response
    {
        return $this->render('board/index.html.twig', [
            'boards' => $boardRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_board_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        BoardTemplateRepository $templateRepository
    ): Response {
        $board = new Board();
        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($board);
            $entityManager->flush();

            return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
        }

        $templates = $templateRepository->findAll();

        return $this->render('board/new.html.twig', [
            'board' => $board,
            'form' => $form,
            'templates' => $templates,
        ]);
    }

    #[Route('/new-from-template/{templateId}', name: 'app_board_new_from_template', methods: ['GET', 'POST'])]
    public function newFromTemplate(
        int $templateId,
        BoardTemplateRepository $templateRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $template = $templateRepository->find($templateId);
        if (!$template) {
            $this->addFlash('error', 'Template not found.');
            return $this->redirectToRoute('app_board_new');
        }

        $board = new Board();
        $board->setName($template->getName());

        $entityManager->persist($board);
        $entityManager->flush();

        // Create columns from template
        $columns = $template->getColumns();
        foreach ($columns as $index => $columnName) {
            $column = new Column();
            $column->setName($columnName);
            $column->setPosition($index);
            $column->setBoard($board);
            $entityManager->persist($column);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Board created from template successfully!');
        return $this->redirectToRoute('app_board_show', ['id' => $board->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_board_show', methods: ['GET'])]
    public function show(Board $board): Response
    {
        return $this->render('board/show.html.twig', [
            'board' => $board,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_board_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Board $board, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('board/edit.html.twig', [
            'board' => $board,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_board_delete', methods: ['POST'])]
    public function delete(Request $request, Board $board, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$board->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($board);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
    }
}

