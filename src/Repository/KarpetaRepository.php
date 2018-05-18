<?php

namespace App\Repository;

use App\Entity\Karpeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Karpeta|null find( $id, $lockMode = null, $lockVersion = null )
 * @method Karpeta|null findOneBy( array $criteria, array $orderBy = null )
 * @method Karpeta[]    findAll()
 * @method Karpeta[]    findBy( array $criteria, array $orderBy = null, $limit = null, $offset = null )
 */
class KarpetaRepository extends ServiceEntityRepository
{
    public function __construct( RegistryInterface $registry )
    {
        parent::__construct( $registry, Karpeta::class );
    }


    /**
     * @param $folderName
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isThisFolderOnMysql( $folderName )
    {

        return $this->createQueryBuilder( 'k' )
                    ->andWhere( 'k.path = :path' )
                    ->setParameter( 'path', $folderName )
                    ->getQuery()
                    ->getOneOrNullResult();
    }


    public function getSidebarFoldersForSarbide( $sarbide )
    {

        $sql = $this->createQueryBuilder( 'k' )
                    ->leftJoin( 'k.permissions', 'p' )
                    ->leftJoin( 'p.taldea', 't' )
                    ->andWhere( 't.name like :sarbide' )
                    ->setParameter( 'sarbide', $sarbide )
                    ->getQuery();




        return $sql->getResult();

    }

    public function isThisFolderAllowed( $folderPath, $sarbide )
    {

        $sql = $this->createQueryBuilder( 'k' )
                    ->innerJoin( 'k.permissions', 'p' )
                    ->innerJoin( 'p.taldea', 't' )
                    ->andWhere( 't.name like :sarbide' )
                    ->setParameter( 'sarbide', $sarbide )
                    ->andWhere( 'k.path like :foldername' )
                    ->setParameter( 'foldername', '%'.$folderPath )
                    ->getQuery();

        return $sql->getResult();

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
