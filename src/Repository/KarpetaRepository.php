<?php

namespace App\Repository;

use App\Entity\Karpeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Karpeta|null find($id, $lockMode = null, $lockVersion = null)
 * @method Karpeta|null findOneBy(array $criteria, array $orderBy = null)
 * @method Karpeta[]    findAll()
 * @method Karpeta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KarpetaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Karpeta::class);
    }


    public function isThisFolderOnMysql($folderName) {
        return $this->createQueryBuilder( 'k' )
                    ->andWhere( 'k.name = :name' )
                    ->setParameter( 'name', $folderName )
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * @return Karpeta[] Returns an array of Karpeta objects
     */
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



    public function findOneBySomeField($value): ?Karpeta
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
