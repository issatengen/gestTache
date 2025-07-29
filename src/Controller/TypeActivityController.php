<?php

namespace App\Controller;

use App\Entity\TypeActivity;
use App\Form\TypeActivityForm;
use App\Repository\TypeActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/type/activity')]
final class TypeActivityController extends AbstractController
{
    
    #[Route(name: 'app_type_activity_index', methods: ['GET'])]
    public function index(TypeActivityRepository $typeActivityRepository): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('type_activity/index.html.twig', [
            'type_activities' => $typeActivityRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $typeActivity = new TypeActivity();
        $form = $this->createForm(TypeActivityForm::class, $typeActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeActivity);
            $entityManager->flush();

            $this->addFlash('success', 'Type d\'activité ajouté avec succès!');
            return $this->redirectToRoute('app_type_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_activity/new.html.twig', [
            'type_activity' => $typeActivity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_activity_show', methods: ['GET'])]
    public function show(TypeActivity $typeActivity): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('type_activity/show.html.twig', [
            'type_activity' => $typeActivity,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_activity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeActivity $typeActivity, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(TypeActivityForm::class, $typeActivity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_activity/edit.html.twig', [
            'type_activity' => $typeActivity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_activity_delete', methods: ['POST'])]
    public function delete(Request $request, TypeActivity $typeActivity, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid('delete'.$typeActivity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typeActivity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_activity_index', [], Response::HTTP_SEE_OTHER);
    }
}
