<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    // Return every card id in the database
    public function findAllCardIds(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_SCALAR_COLUMN);
    }

    public function findCardByName(string $name): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }
}
