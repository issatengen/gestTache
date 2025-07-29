<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\SubTask;
use App\Entity\Department;
use App\Form\UserForm;
use App\Repository\UserRepository;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Route('/user')]
final class UserController extends AbstractController
{

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(DepartmentRepository $departmentRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) { 
            return $this->redirectToRoute('app_login');
        }
        $qb= $entityManager->createQueryBuilder();
        $qb->select('u as user, SUM(st.timeAllocated) as totalHours')
           ->from('App\Entity\User','u')
           ->join('u.subTasks','st')
           ->groupBy('u.id');
        $results = $qb->getQuery()->getResult();
        $users= array_map(function($item){
            return [
                'id' =>$item['user']->getId(),
                'profilePicture' =>$item['user']->getProfilePicture(),
                'surname' => $item['user']->getSurname(),
                'name' => $item['user']->getName(),
                'telephone' => $item['user']->getTelephone(),
                'email' => $item['user']->getEmail(),
                'department' => $item['user']->getDepartment(),
                'totalHours' => $item['totalHours']
            ];
        }, $results);

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'debut' => null,
            'fin' => null,
            'departmentX' => null,
            'departments' => $departmentRepository->findAll()
        ]);
    }

    #[Route('/search', name: 'app_user_index_search', methods: ['GET'])]
    public function indexSearch(DepartmentRepository $departmentRepository, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }
        
        $debut = $request->query->get('debut');
        $fin = $request->query->get('fin');
        $departmentId = $request->get('department');
        $department = $entityManager->getRepository(Department::class)->find($departmentId);

        $qb = $entityManager->createQueryBuilder();
        $qb->select('u as user, SUM(st.timeAllocated) as totalHours')
            ->from('App\Entity\User', 'u')
            ->leftJoin('u.subTasks', 'st')
            ->andWhere('u.department = :department')
            ->andWhere('st.debut >= :debut')
            ->andWhere('st.fin <= :fin')
            ->setParameter('debut', new \DateTime($debut))
            ->setParameter('department', $department)
            ->setParameter('fin', new \DateTime($fin))
            ->groupBy('u.id');

        $results = $qb->getQuery()->getResult();

        // Transform the results to match your template expectations
        $users = array_map(function($item) {
            return [
                'id' => $item['user']->getId(),
                'profilePicture' => $item['user']->getProfilePicture(),
                'surname' => $item['user']->getSurname(),
                'name' => $item['user']->getName(),
                'telephone' => $item['user']->getTelephone(),
                'email' => $item['user']->getEmail(),
                'department' => $item['user']->getDepartment(),
                'totalHours' => $item['totalHours']
            ];
        }, $results);

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'debut' => $debut,
            'fin' => $fin,
            // 'departmentX' => $departmentRepository->findBy(['department' => $department]),
            'departmentX' => null,
            'departments' => $departmentRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $pwh,
        #[Autowire('upload_images')] string $location
        ): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $user = new User();
        $form = $this->createForm(UserForm::class, $user, [
            'include_password' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = password_hash($user->getPassword(), PASSWORD_DEFAULT);
            $user->setPassword($password);

            $image= $form->get('profile_picture')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $location,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }
                $user->setProfilePicture($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // envoie de la notification
            $this->addFlash('success', 'Compte créer avec succès');

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $subTasks = $entityManager->getRepository(SubTask::class)->findBy(['user' => $user]);
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'subTasks' => $subTasks,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === null ) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
