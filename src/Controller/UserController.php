<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/users/list', name: 'user_list',  methods: ['GET'])]
    public function findUsers(Request $request): Response
    {
        $firstname = $request->query->get('firstname');
        $lastname = $request->query->get('Lastname');

        $users = $this->userService->findUsers($firstname, $lastname);

        return new JsonResponse($users);
    }

    #[Route('/user/get/{id}', name: 'user_get', methods: ['GET'])]
    public function findUser(int $id): Response
    {
        try {
            $user = $this->userService->findUserById($id);
            return $this->json($user);
        } catch (NotFoundHttpException $e) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/user/create', name: 'user_create', methods: ['POST'])]
    public function newUser(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $firstname = $data['firstname'] ?? null;
            $lastname = $data['lastname'] ?? null;

            $user = $this->userService->createUser($firstname, $lastname);

            return new JsonResponse($user, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An Error occured']);
        }
    }

    #[Route('/user/update/{id}', name: 'user_update', methods: ['PUT'])]
    public function updateUserAction(Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;

        try {
            $user = $this->userService->updateUser($id, $firstname, $lastname);
            return $this->json($user);
        } catch (NotFoundHttpException $e) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/user/delete/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteUserAction(int $id): Response
    {
        try {
            $this->userService->deleteUser($id);
            return $this->json(['message' => 'User deleted']);
        } catch (NotFoundHttpException $e) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
