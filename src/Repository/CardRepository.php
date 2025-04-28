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

    public function findCardByNameAndLanguage(string $name, string $language): array
    {
        $languageColumn = match ($language) {
            'fr' => 'c.fr_name',
            'de' => 'c.de_name',
            'it' => 'c.it_name',
            'pt' => 'c.pt_name',
            default => 'c.name',
        };
        return $this->createQueryBuilder('c')
            ->andWhere($languageColumn . ' LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }
}
