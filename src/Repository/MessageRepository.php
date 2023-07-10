<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function add(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /**
     * Custom request to get the number of active received messages
     * active means that the messages are still existant in the sender messagesbox so they still exist in the database
     * a message is deleted from the database only when active and active2 properties are at 0 which means that both the receiver
     * and the sender deleted it.
     */
    public function findAllMessageActive2(int $id)
    {
        return $this->createQueryBuilder('m')
        ->where('m.recipient = :Id')
        ->andWhere('m.Active2 = :Active2')
        ->setParameter('Id', $id)
        ->setParameter('Active2', '1')
        ->getQuery()
        ->getResult();
    }

     /**
     * Custom request to get the number of active sent messages
     * active means that the messages are still existant in the receiver messagesbox so they still exist in the database
     * a message is deleted from the database only when active and active2 properties are at 0 which means that both the receiver
     * and the sender deleted it.
     */
    public function findAllMessageActive(int $id)
    {
        return $this->createQueryBuilder('m')
        ->where('m.sender = :Id')
        ->andWhere('m.Active = :Active')
        ->setParameter('Id', $id)
        ->setParameter('Active', '1')
        ->getQuery()
        ->getResult();  
    }

//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
