<?php

namespace App\Repository;

use App\Entity\MesMains;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MesMains|null find($id, $lockMode = null, $lockVersion = null)
 * @method MesMains|null findOneBy(array $criteria, array $orderBy = null)
 * @method MesMains[]    findAll()
 * @method MesMains[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MesMainsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MesMains::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(MesMains $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(MesMains $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getWorthsHands(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.win IS NOT NULL')
            ->orderBy('m.win','ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return MesMains[] Returns an array of MesMains objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MesMains
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
