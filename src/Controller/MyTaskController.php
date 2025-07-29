<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use App\Entity\Department;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyTaskController extends AbstractController
{
    #[Route('/my/task/{id}', name: 'app_my_task_index', methods: ['GET'])]
    public function index( $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        $tasks = $entityManager->createQuery(
            'SELECT t FROM App\Entity\Task t JOIN t.user u WHERE u.id = :userId'
        )->setParameter('userId', $id)->getResult();
        return $this->render('my_task/index.html.twig', [
            'tasks' => $tasks,
            'user' => $user,
        ]);
    }
}
