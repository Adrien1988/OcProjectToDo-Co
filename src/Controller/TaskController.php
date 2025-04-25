<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{


    public function __construct(private readonly EntityManagerInterface $em)
    {
    }


    #[Route('/tasks', name: 'task_list')]
    public function list(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAllWithAuthor();

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }


    #[Route('/tasks/create', name: 'task_create')]
    public function create(Request $request): Response
    {

        $this->denyAccessUnlessGranted(TaskVoter::CREATE);

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());
            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }


    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function edit(int $id, Request $request): Response
    {
        $task = $this->em->find(Task::class, $id)
            ?? throw $this->createNotFoundException('Tâche non trouvée');

        $this->denyAccessUnlessGranted(TaskVoter::EDIT, $task);

        $form = $this->createForm(TaskType::class, $task)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }


    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(int $id): Response
    {
        $task = $this->em->find(Task::class, $id)
            ?? throw $this->createNotFoundException('Tâche non trouvée');

        $this->denyAccessUnlessGranted(TaskVoter::EDIT, $task);

        $task->toggle(!$task->isDone());
        $this->em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle(), $task->isDone() ? 'faite' : 'à faire'));

        return $this->redirectToRoute('task_list');
    }


    #[Route('/tasks/{id}/delete', name: 'task_delete', methods: ['POST', 'GET'])]
    public function deleteTask(int $id): Response
    {
        $task = $this->em->find(Task::class, $id)
            ?? throw $this->createNotFoundException('Tâche non trouvée');

        $this->denyAccessUnlessGranted(TaskVoter::DELETE, $task);

        $this->em->remove($task);
        $this->em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }


}
