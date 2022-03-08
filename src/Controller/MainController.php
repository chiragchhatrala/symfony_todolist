<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Todo;
use Doctrine\Persistence\ManagerRegistry;

class MainController extends AbstractController
{
    public function index()
    {
        return $this->render('home.html.twig', []);
    }

    public function fetch(ManagerRegistry $doctrine): Response
    {
        $todoList = $doctrine->getRepository(Todo::class)->fetchTodoList();

        // echo "<pre>";print_r($todoList);die;
        return $this->json(["status" => true, "data" => $todoList]);
    }

    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $post_data = json_decode($request->getContent(), true);
        if (isset($post_data["name"]) && $post_data["name"]) {
            $entityManager = $doctrine->getManager();

            $todo = new Todo();
            $todo->setName($post_data["name"]);
            $todo->setStatus(1);
            $todo->setCreatedAt(new \DateTime());

            $entityManager->persist($todo);
            $entityManager->flush();

            return $this->json(["status" => true, "data" => $todo->getId()]);
        } else {
            return $this->json(["status" => false, "data" => false]);
        }
    }

    public function update(ManagerRegistry $doctrine, Request $request): Response
    {
        $post_data = json_decode($request->getContent(), true);
        if (isset($post_data["id"]) && $post_data["id"]) {
            $entityManager = $doctrine->getManager();
            $record = $entityManager->find('App\Entity\Todo', $post_data["id"]);
            if ($record) {
                if (isset($post_data["name"]) && $post_data["name"]) {
                    $record->setName($post_data["name"]);
                }
                if (isset($post_data["status"])) {
                    $record->setStatus((int)$post_data["status"]);
                    $record->setCompletedAt(($post_data["status"] == 2) ? new \DateTime() : null);
                }
                $entityManager->flush();

                return $this->json(["status" => true, "data" => $post_data["id"]]);
            }
        }
        return $this->json(["status" => false, "data" => false]);
    }

    public function remove(ManagerRegistry $doctrine, Request $request): Response
    {
        $post_data = json_decode($request->getContent(), true);
        if (isset($post_data["id"]) && $post_data["id"]) {
            $entityManager = $doctrine->getManager();
            $singletodo = $entityManager->find('App\Entity\Todo', $post_data["id"]);
            $entityManager->remove($singletodo);
            $entityManager->flush();

            return $this->json(["status" => true, "data" => $post_data["id"]]);
        } else {
            return $this->json(["status" => false, "data" => false]);
        }
    }
}
