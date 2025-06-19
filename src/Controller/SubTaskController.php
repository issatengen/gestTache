<?php

namespace App\Controller;

use App\Entity\SubTask;
use App\Form\SubTaskForm;
use App\Repository\SubTaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sub/task')]
final class SubTaskController extends AbstractController
{
    #[Route(name: 'app_sub_task_index', methods: ['GET'])]
    public function index(SubTaskRepository $subTaskRepository): Response
    {
        return $this->render('sub_task/index.html.twig', [
            'sub_tasks' => $subTaskRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sub_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subTask = new SubTask();
        $form = $this->createForm(SubTaskForm::class, $subTask);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subTask);
            $entityManager->flush();

            $this->addFlash('success', 'Sub-tâche ajuter avec succès!');
            return $this->redirectToRoute('app_sub_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sub_task/new.html.twig', [
            'sub_task' => $subTask,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sub_task_show', methods: ['GET'])]
    public function show(SubTask $subTask): Response
    {
        return $this->render('sub_task/show.html.twig', [
            'sub_task' => $subTask,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sub_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubTask $subTask, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubTaskForm::class, $subTask);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sub_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sub_task/edit.html.twig', [
            'sub_task' => $subTask,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sub_task_delete', methods: ['POST'])]
    public function delete(Request $request, SubTask $subTask, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subTask->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($subTask);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sub_task_index', [], Response::HTTP_SEE_OTHER);
    }
}
