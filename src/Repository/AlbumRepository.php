<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Universe;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Album>
 *
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function add(Album $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Album $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * custom request in order to search an album(title) by keyword
     */
    public function findBySearch($keyword)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.title Like :key')
            ->setParameter('key', "%{$keyword}%")
            ->getQuery()
            ->getResult();
    }


     /**
     * return all collectibles ordered by their creation date
     */
    public function findAllOrderedByCreationDate()
    {
        return $this->createQueryBuilder('c')
        ->orderBy('c.createdAt', 'DESC')
        ->setMaxResults('3')
        ->getQuery()
        ->getResult();
    }

    /**
    * return all albums related to an universe
    */
    public function findAllByUnivers(Universe $universe)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.universeId', 'u')
            ->andWhere('u = :universe')
            ->setParameter('universe', $universe)
            ->getQuery()
            ->getResult();
    }


    public function paginateAll($page = 1, $pageSize = 21)
    {
        // Create a query builder instance for entity 'a'
        $queryBuilder = $this->createQueryBuilder('a')
            ->getQuery();

        // Create a Paginator object and pass the query builder
        $paginator = new Paginator($queryBuilder);
        $paginator->setUseOutputWalkers(false);

        // Configure the pagination settings
        $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $pageSize) // Set the starting result index
            ->setMaxResults($pageSize); // Set the maximum number of results per page

        // Return the paginator object
        return $paginator;
    }

//    /**
//     * @return Album[] Returns an array of Album objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Album
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
