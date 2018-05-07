<?php

namespace App\Repository;

use App\Entity\Taldea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Taldea|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taldea|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taldea[]    findAll()
 * @method Taldea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaldeaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Taldea::class);
    }

//    /**
//     * @return Taldea[] Returns an array of Taldea objects
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
    public function findOneBySomeField($value): ?Taldea
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
