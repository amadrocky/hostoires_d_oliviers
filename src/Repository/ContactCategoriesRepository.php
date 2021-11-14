<?php

namespace App\Repository;

use App\Entity\ContactCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactCategories|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactCategories|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactCategories[]    findAll()
 * @method ContactCategories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactCategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactCategories::class);
    }

    // /**
    //  * @return ContactCategories[] Returns an array of ContactCategories objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContactCategories
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
