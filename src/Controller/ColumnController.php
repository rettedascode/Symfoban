<?php

namespace App\Controller;

use App\Entity\Column;
use App\Form\ColumnType;
use App\Repository\ColumnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/column')]
class ColumnController extends AbstractController
{
    #[Route('/new', name: 'app_column_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $column = new Column();
        $form = $this->createForm(ColumnType::class, $column);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($column);
            $entityManager->flush();

            return $this->redirectToRoute('app_board_show', ['id' => $column->getBoard()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('column/new.html.twig', [
            'column' => $column,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_column_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Column $column, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ColumnType::class, $column);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_board_show', ['id' => $column->getBoard()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('column/edit.html.twig', [
            'column' => $column,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_column_delete', methods: ['POST'])]
    public function delete(Request $request, Column $column, EntityManagerInterface $entityManager): Response
    {
        $boardId = $column->getBoard()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$column->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($column);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_board_show', ['id' => $boardId], Response::HTTP_SEE_OTHER);
    }
}

