<?php

namespace App\Repository;

use App\Entity\Merchants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Merchants|null find($id, $lockMode = null, $lockVersion = null)
 * @method Merchants|null findOneBy(array $criteria, array $orderBy = null)
 * @method Merchants[]    findAll()
 * @method Merchants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MerchantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Merchants::class);
    }

    // /**
    //  * @return Merchants[] Returns an array of Merchants objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Merchants
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
