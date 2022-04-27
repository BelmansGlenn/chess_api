<?php

namespace App\Repository;

use App\Entity\PlayerScore;
use App\Repository\Interface\SearchInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerScore[]    findAll()
 * @method PlayerScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerScoreRepository extends ServiceEntityRepository implements SearchInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerScore::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PlayerScore $entity, bool $flush = true): void
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
    public function remove(PlayerScore $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PlayerScore[] Returns an array of PlayerScore objects
    //  */
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
    public function findOneBySomeField($value): ?PlayerScore
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function search($term, $order, $limit, $offset, $fields = [])
    {
        $qb = $this
            ->createQueryBuilder('p');
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                if ($key === 'id')
                {
                        $qb->andWhere("p.TournamentId = :val")
                        ->setParameter("val", $value);
                }else{

                    $qb->andWhere("p.$key = :key");
                    $qb->setParameter("key", $value);
                }
            }
        }
        $qb->orderBy('p.id', $order)
            ->setFirstResult($offset-1)
            ->setMaxResults($limit);
        return $qb->getQuery()->getResult();

    }

    public function countValue($fields = [])
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->select('count(p.id)');

        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                if ($key === 'id')
                {
                    $qb->andWhere("p.TournamentId = :val")
                        ->setParameter("val", $value);
                }else{

                    $qb->andWhere("p.$key = :key");
                    $qb->setParameter("key", $value);
                }
            }
        }
        return $qb->getQuery()->getSingleScalarResult();
    }



    public function findTournamentPreviousRound($round, $tournamentId)
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->andWhere('p.round = :val')
            ->setParameter('val', $round)
            ->andWhere('p.TournamentId = :id')
            ->setParameter('id', $tournamentId);

        return $qb->getQuery()->getResult();
    }

    public function findPlayerByIdAndPreviousRound($playerId,$round, $tournamentId)
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->andWhere('p.player = :id')
            ->setParameter('id', $playerId)
            ->andWhere('p.round = :val')
            ->setParameter('val', $round)
            ->andWhere("p.TournamentId = :tid")
            ->setParameter('tid', $tournamentId);


        return $qb->getQuery()->getOneOrNullResult();
    }
}
