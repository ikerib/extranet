<?php

namespace App\Repository;

use App\Entity\Karpetak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Karpetak|null find($id, $lockMode = null, $lockVersion = null)
 * @method Karpetak|null findOneBy(array $criteria, array $orderBy = null)
 * @method Karpetak[]    findAll()
 * @method Karpetak[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KarpetakRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Karpetak::class);
    }

//    /**
//     * @return Karpetak[] Returns an array of Karpetak objects
//     */
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
    public function findOneBySomeField($value): ?Karpetak
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
