<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;



/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * @param int $currentPage
     * @return Paginator
     */
    public function getAllTricks($currentPage)
    {
        // Create our query
        $query = $this->createQueryBuilder('t')
            ->getQuery();

        // No need to manually get get the result ($query->getResult())

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }

    /**
     * @param $sql
     * @param int $page
     * @param int $limit
     * @return Paginator
     */
    public function paginate($sql, $page, $limit = 4)
    {
        $paginator = new Paginator($sql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }
}
