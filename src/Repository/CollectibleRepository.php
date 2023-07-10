<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Collectible;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Collectible>
 *
 * @method Collectible|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collectible|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collectible[]    findAll()
 * @method Collectible[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectibleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collectible::class);
    }

    public function add(Collectible $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Collectible $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * custom request in order to search an collectible(name) by keyword
     */
    public function findBySearch($keyword)
    {
        return $this->createQueryBuilder('c')
            ->Where('c.name Like :key')
            ->setParameter('key', "%{$keyword}%")
            ->getQuery()
            ->getResult();
    }

    /**
    * return all collectibles related to a categorie
    */
    public function findAllByCategory(Category $category)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.Categories', 'cat')
            ->andWhere('cat = :category')
            ->setParameter('category', $category)
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
//     * @return Collectible[] Returns an array of Collectible objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Collectible
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
