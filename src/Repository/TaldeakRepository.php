<?php

namespace App\Repository;

use App\Entity\Taldeak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Taldeak|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taldeak|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taldeak[]    findAll()
 * @method Taldeak[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaldeakRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Taldeak::class);
    }

//    /**
//     * @return Taldeak[] Returns an array of Taldeak objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Taldeak
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
