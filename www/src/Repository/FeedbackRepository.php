<?php

namespace App\Repository;

use App\Entity\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feedback>
 *
 * @method Feedback|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feedback|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feedback[]    findAll()
 * @method Feedback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbackRepository extends ServiceEntityRepository
{
    private const ELEMENTS_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    public function getList(int $page): array
    {
        $page = $page > 0 ? $page-1 : 0;

        return $this->createQueryBuilder('f')
            ->setMaxResults(self::ELEMENTS_PER_PAGE)
            ->setFirstResult($page*self::ELEMENTS_PER_PAGE)
            ->getQuery()
            ->getResult();
    }

    public function getByID(int $id): Feedback|null
    {
        return $this->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
