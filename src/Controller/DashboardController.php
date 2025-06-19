<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        // Here you can fetch data for the dashboard, e.g. stats, recent tasks, etc.
        // Example: $tasks = $this->getDoctrine()->getRepository(Task::class)->findAll();

        // Pass any required data to the template
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            // 'tasks' => $tasks, // Uncomment and use if you fetch tasks
            // Add other variables as needed
        ]);
    }
}
