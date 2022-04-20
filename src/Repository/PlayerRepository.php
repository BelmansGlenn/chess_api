<?php

namespace App\Repository;

use App\Entity\Player;
use App\Repository\Interface\SearchInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository implements SearchInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Player $entity, bool $flush = true): void
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
    public function remove(Player $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Player[] Returns an array of Player objects
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
    public function findOneBySomeField($value): ?Player
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
            ->createQueryBuilder('p')
            ->orderBy('p.lastname', $order);


        if ($term){
            $qb
                ->where('p.lastname LIKE ?1')
                ->setParameter(1, '%'.$term.'%');
        }
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                $qb->leftJoin('p.tournaments', 'tournaments');
                $qb->andWhere("tournaments.$key = :key");
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
            ->createQueryBuilder('p')
            ->select('count(p.id)');
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                $qb->leftJoin('p.tournaments', 'tournaments');
                $qb->andWhere("tournaments.$key = :key");
                $qb->setParameter("key", $value);
            }
        }

        return $qb->getQuery()->getSingleScalarResult();
    }


}
