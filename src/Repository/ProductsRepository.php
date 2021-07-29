<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    // /**
    //  * @return Products[] Returns an array of Products objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Products
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */



    public function findAllWithParams($limit = null, $offset = null, $order = 'DESC', $orderBy = 'id', $q = null): array
    {

        $qb = $this->createQueryBuilder('p')
            ->orderBy("p.{$orderBy}", $order)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if (!is_null($q)) {
            $qb->where("lower(p.name) like lower(:name)")->setParameter("name", "%" . $q . "%");
        }

        $query = $qb->getQuery();

        return $query->execute();
    }
    public function countAll(): ?int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            //->useQueryCache(true)
            //->enableResultCache(3600, 'my_custom_id')
            ->getSingleScalarResult();
    }
    public function deleteById($id): ?bool
    {
        $em = $this->getEntityManager();
        $product = $this->findOneBy(array('id' => $id));

        if ($product != null) {
            $em->remove($product);
            $em->flush();
        }
        return true;
    }
}
