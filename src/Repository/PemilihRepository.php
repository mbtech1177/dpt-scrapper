<?php

namespace App\Repository;

use App\Entity\Pemilih;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Pemilih|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pemilih|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pemilih[]    findAll()
 * @method Pemilih[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PemilihRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pemilih::class);
    }

//    /**
//     * @return Pemilih[] Returns an array of Pemilih objects
//     */
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
    public function findOneBySomeField($value): ?Pemilih
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
