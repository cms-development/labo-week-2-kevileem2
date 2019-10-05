<?php

namespace App\Repository;

use App\Entity\Kampen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Kampen|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kampen|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kampen[]    findAll()
 * @method Kampen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KampenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Kampen::class);
    }

    // /**
    //  * @return Kampen[] Returns an array of Kampen objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Kampen
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
