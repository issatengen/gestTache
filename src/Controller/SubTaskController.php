<?php

namespace App\Controller;

use App\Entity\SubTask;
use App\Entity\Task;
use App\Entity\User;
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
    
    #[Route('/{id}', name: 'app_sub_task_index', methods: ['GET'])]
    public function index($id ,SubTaskRepository $subTaskRepository, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $taskId = $entityManager->getRepository(Task::class)->find($id);
        if (!$taskId) {
            throw $this->createNotFoundException('Task not found');
        }
        $subTasks = $subTaskRepository->findBy(['task' => $taskId]);

        // Vérification automatique du statut "en retard"
        $now = new \DateTime();
        $updated = false;
        foreach ($subTasks as $subTask) {
            if (
                $subTask->getFin() !== null &&
                $subTask->getStatus() !== 'T' && // pas terminée
                $subTask->getStatus() !== 'D' && // pas déjà en retard
                $subTask->getFin() < $now
            ) {
                $subTask->setStatus('D');
                $entityManager->persist($subTask);
                $updated = true;
            }
        }
        if ($updated) {
            $entityManager->flush();
        }

        return $this->render('sub_task/index.html.twig', [
            'sub_tasks' => $subTasks,
            'task' => $taskId,
            'dateNow' => new \DateTime(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_sub_task_new', methods: ['GET', 'POST'])]
    public function new($id ,Request $request, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $subTask = new SubTask();
        $form = $this->createForm(SubTaskForm::class, $subTask);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskId = $entityManager->getRepository(Task::class)->find($id);
            if (!$taskId) {
                throw $this->createNotFoundException('Task not found');
            }
            $subTask->setTask($taskId);
            $subTask->setStatus('F');
            $entityManager->persist($subTask);
            $entityManager->flush();

            $this->addFlash('success', 'Sous-tâche ajouter avec succès!');
            return $this->redirectToRoute('app_sub_task_index', ['id' => $taskId->getId() ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sub_task/new.html.twig', [
            'sub_task' => $subTask,
            'form' => $form,
            'task' => $entityManager->getRepository(Task::class)->find($id),
        ]);
    }

    #[Route('/{id}', name: 'app_sub_task_show', methods: ['GET'])]
    public function show(SubTask $subTask): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('sub_task/show.html.twig', [
            'sub_task' => $subTask,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sub_task_edit', methods: ['GET', 'POST'])]
    public function edit($id, Request $request, SubTask $subTask, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(SubTaskForm::class, $subTask); 
        $form->handleRequest($request);
        if (!$subTask) {
            throw $this->createNotFoundException('Sub-task not found');
        }
        $subTaskName = $entityManager->getRepository(SubTask::class)->find($id); 

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subTask);
            $entityManager->flush();

            return $this->redirectToRoute('app_sub_task_index',['id' => $id ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sub_task/edit.html.twig', [
            'sub_task' => $subTask,
            'Stask' => $subTaskName,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sub_task_delete', methods: ['POST'])]
    public function delete($id, Request $request, SubTask $subTask, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid('delete'.$subTask->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($subTask);
            $entityManager->flush();
            $this->addFlash('success', 'Sous-tâche supprimée avec succès!');
        }

        return $this->redirectToRoute('app_task_index',[], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pending', name: 'app_sub_pending', methods: ['GET', 'POST'])]
    public function pending($id, Request $request, SubTask $subTask, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }

        $subTask->setStatus('P'); // Set the status to 'P' for pending
        $entityManager->persist($subTask);
        $entityManager->flush();
        $this->addFlash('success', 'Le début de l\'exécution à été enregistrer!');

        return $this->redirectToRoute('app_sub_task_index', ['id' => $subTask->getTask()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/terminated', name: 'app_sub_terminated', methods: ['GET', 'POST'])]
    public function terminated($id, Request $request, SubTask $subTask, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }

        $subTask->setStatus('T'); // Set the status to 'T' for terminated
        $entityManager->persist($subTask);
        $entityManager->flush();

        $this->addFlash('success', 'Sous-tâche terminée avec succès!');

        return $this->redirectToRoute('app_sub_task_index', ['id' => $subTask->getTask()->getId()], Response::HTTP_SEE_OTHER);
    }

}
