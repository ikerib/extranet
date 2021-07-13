<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findAll()
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }


    public function canUpload( $folderPath, $sarbide )
    {

        $sql = $this->createQueryBuilder( 'p' )
                    ->innerJoin( 'p.karpeta', 'k' )
                    ->innerJoin( 'p.taldea', 't' )
                    ->andWhere( 't.name = :sarbide' )
                    ->setParameter( 'sarbide', $sarbide )
                    ->andWhere( 'k.path like :foldername' )
                    ->setParameter( 'foldername', '%'.$folderPath )
                    ->getQuery();

        return $sql->getResult();

    }


}
