<?php

namespace App\Repository;

use App\Entity\User_;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User_::class);
    }


    public function findUsers(?string $firstname, ?string $lastname): array
    {
        $queryBuilder = $this->createQueryBuilder('u');
        
        // Filter by firstname if it exists
        if ($firstname !== null) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('LOWER(u.firstname)', ':firstname'))
                ->setParameter('firstname', strtolower($firstname));
        }
        
        // Filter by lastname if it exists
        if ($lastname !== null) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('LOWER(u.lastname)', ':lastname'))
                ->setParameter('lastname', strtolower($lastname));
        }
        
        return $queryBuilder->getQuery()->getResult();
    }
    
    
    public function findById(int $id): ?User_
    {
        return $this->find($id);
    }
}