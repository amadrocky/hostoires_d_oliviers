<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    /**
     *
     * @param string $email
     * @return array
     */
    public function findByUser(string $email): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.email = :email')
            ->andWhere('o.workflowState = :workflow_state')
            ->setParameters([
                'workflow_state' => 'paid',
                'email' => $email
            ])
            ->orderBy('o.modifiedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Orders by period
     *
     * @param \DateTime $start_date
     * @param \DateTime $end_date
     * @return array
     */
    public function findByPeriod(\DateTime $start_date, \DateTime $end_date): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.createdAt >= :start_date AND o.createdAt <= :end_date')
            ->andWhere('o.workflowState = :workflow_state')
            ->setParameters([
                'workflow_state' => 'paid',
                'start_date' => $start_date,
                'end_date' => $end_date
                ])
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Orders[] Returns an array of Orders objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Orders
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
