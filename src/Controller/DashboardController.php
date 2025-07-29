<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }
        
        $countUsers = $entityManager->getRepository(User::class)->count([]);
        $countDepartments = $entityManager->getRepository(Department::class)->count([]);
        $countTasks = $entityManager->getRepository(Task::class)->count([]);

        

        // Pass any required data to the template
        return $this->render('dashboard/index.html.twig', [
            'countUsers' => $countUsers,
            'countDepartments' => $countDepartments,
            'countTasks' => $countTasks,
            // 'tasks' => $tasks, // Uncomment and use if you fetch tasks
            // Add other variables as needed
        ]);
    }
}
