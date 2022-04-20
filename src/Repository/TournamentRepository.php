<?php

namespace App\Repository;

use App\Entity\Tournament;
use App\Repository\Interface\SearchInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tournament|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tournament|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tournament[]    findAll()
 * @method Tournament[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TournamentRepository extends ServiceEntityRepository implements SearchInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tournament::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Tournament $entity, bool $flush = true): void
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
    public function remove(Tournament $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Tournament[] Returns an array of Tournament objects
    //  */
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
    public function findOneBySomeField($value): ?Tournament
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function search($term, $order, $limit, $offset, $fields = [])
    {
        $qb = $this
            ->createQueryBuilder('t')
            ->andWhere('t.isFinished = false')
            ->orderBy('t.startedAt', $order);

        if ($term){
            $qb
                ->where('t.name LIKE ?1')
                ->setParameter(1, '%'.$term.'%');
        }

        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                $qb->andWhere("t.$key = :key");
                $qb->setParameter("key", $value);
            }
        }

        $qb
            ->setFirstResult($offset-1)
            ->setMaxResults($limit);
        return $qb->getQuery()->getResult();

    }

    public function countValue($fields = [])
    {
        $qb = $this
            ->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.isFinished = false');
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                $qb->andWhere("t.$key = :key");
                $qb->setParameter("key", $value);
            }
        }
        return $qb->getQuery()->getSingleScalarResult();
    }


    public function findPlayerByTournament($id, $userId)
    {
        $qb = $this
            ->createQueryBuilder('t')
            ->andWhere('t.id = :val')
            ->setParameter('val', $id)
            ->leftJoin('t.players', 'players')
            ->andWhere('players.id = :user')
            ->select('players.id')
            ->setParameter('user', $userId);

        return $qb->getQuery()->getResult();
    }

}
