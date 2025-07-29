<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Department;
use App\Form\TaskForm;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task')]
final class TaskController extends AbstractController
{
    
    #[Route(name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $departments= $entityManager->getRepository(Department::class)->findAll();
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
            'departments' => $departments,
        ]);
    }

    #[Route('/new/{id}', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $task = new Task();
        $form = $this->createForm(TaskForm::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $task->setDepartment($entityManager->getRepository(Department::class)->find($id));
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Task created successfully!');
            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(TaskForm::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }
}
