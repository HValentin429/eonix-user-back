<?php

namespace App\Service;

use App\Entity\User_;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    private $userRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function findUsers(?string $firstname, ?string $lastname): array
    {
        return $this->userRepository->findUsers($firstname, $lastname);
    }
    
    public function findUserById(int $id): User_
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return $user;
    }

    public function createUser(string $firstname, string $lastname): User_
    {

        if (!$firstname || !$lastname) {
            return new Response('Firstname and Lastname are required');
        }

        $user = new User_();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(int $id, string $firstname, string $lastname): User_
    {
        if (!$firstname || !$lastname) {
            return $this->json(['error' => 'Firstname and Lastname are required'], Response::HTTP_BAD_REQUEST);
        }
        
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }



        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(int $id): void
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
