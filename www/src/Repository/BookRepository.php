<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function getBooksList(int $page, int $per_page): array|null
    {
        $page = $page > 0 ? $page-1 : 0;

        return $this->createQueryBuilder('b')
            ->setMaxResults($per_page)
            ->setFirstResult($page*$per_page)
            ->getQuery()
            ->getResult();
    }

    public function getBooksListCount(): int
    {
        return $this->createQueryBuilder('b')
            ->select('count(b.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getBookByID(int $id): Book|null
    {
        return $this->createQueryBuilder('b')
            ->where('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function searchByQuery(Request $request): array|null
    {
        return $this->createQueryBuilder('b')
            ->where('b.title LIKE :query')
            ->setParameter('query', '%'.$request->get('query').'%')
            ->setMaxResults(20)
            ->getQuery()->getResult();
    }
}
