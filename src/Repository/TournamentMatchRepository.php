<?php

namespace App\Repository;

use App\Entity\TournamentMatch;
use App\Repository\Interface\SearchInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TournamentMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method TournamentMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method TournamentMatch[]    findAll()
 * @method TournamentMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TournamentMatchRepository extends ServiceEntityRepository implements SearchInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TournamentMatch::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TournamentMatch $entity, bool $flush = true): void
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
    public function remove(TournamentMatch $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return TournamentMatch[] Returns an array of TournamentMatch objects
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
    public function findOneBySomeField($value): ?TournamentMatch
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
            ->createQueryBuilder('t');
             if (count($fields) > 0) {
                 foreach ($fields as $key => $value) {
                     if ($key === 'id')
                     {
                         $qb->leftJoin('t.tournament', 'tournament')
                             ->andWhere("tournament.$key = :val")
                             ->setParameter("val", $value);
                     }else{

                     $qb->andWhere("t.$key = :key");
                     $qb->setParameter("key", $value);
                     }
                 }
             }
            $qb->orderBy('t.id', $order)
            ->setFirstResult($offset-1)
            ->setMaxResults($limit);
        return $qb->getQuery()->getResult();

    }

    public function countValue($fields = [])
    {
        $qb = $this
            ->createQueryBuilder('t')
            ->select('count(t.id)');

        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                if ($key === 'id')
                {
                    $qb->leftJoin('t.tournament', 'tournament')
                        ->andWhere("tournament.$key = :val")
                        ->setParameter("val", $value);
                }else{

                $qb->andWhere("t.$key = :key");
                $qb->setParameter("key", $value);
                }
            }
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findTournamentMatchCurrentRound($round, $tournamentId)
    {
        $qb = $this
            ->createQueryBuilder('t')
            ->andWhere('t.round = :val')
            ->setParameter('val', $round)
            ->leftJoin('t.tournament', 'tournament')
            ->andWhere("tournament.id = :id")
            ->setParameter("id", $tournamentId)
            ->leftJoin('t.white', 'white')
            ->addSelect('white')
            ->leftJoin('t.black', 'black')
            ->addSelect('black');

        return $qb->getQuery()->getResult();
    }


}
