<?php

// src/Repository/TaskRepository.php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }


    /**
     * Récupère toutes les tâches avec leur auteur (évitant le N+1).
     *
     * @return Task[]
     */
    public function findAllWithAuthor(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'a')
            ->addSelect('a')          // ← fetch join
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


}
