<?php

namespace App\Repository;

use App\Entity\AlbumLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AlbumLike>
 *
 * @method AlbumLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlbumLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlbumLike[]    findAll()
 * @method AlbumLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlbumLike::class);
    }

    public function add(AlbumLike $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AlbumLike $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /**
     * return the 3 albums with the most number of likes
     */
    public function findAllOrderedByNbersOfLikes()
    {
        return $this->createQueryBuilder('a')
        ->groupBy("a.album")
        ->orderBy('count(a.user)', "DESC")
        ->setMaxResults('3')
        ->getQuery()
        ->getResult();  
        }


//    /**
//     * @return AlbumLike[] Returns an array of AlbumLike objects
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

//    public function findOneBySomeField($value): ?AlbumLike
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
