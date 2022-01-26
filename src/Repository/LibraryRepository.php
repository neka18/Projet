<?php

namespace App\Repository;

use App\Entity\Library;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Library|null find($id, $lockMode = null, $lockVersion = null)
 * @method Library|null findOneBy(array $criteria, array $orderBy = null)
 * @method Library[]    findAll()
 * @method Library[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LibraryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Library::class);
    }

    public function getAnimeLibraryBy($userId, $animeId)
    {

        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.utilisateur', 'u')
            ->addSelect('u')
            ->leftJoin('l.anime', 'a')
            ->addSelect('a')
            ->select('l.id')
            ->where('u.id = :userId')
            ->andWhere('a.id = :animeId')
            ->setParameters(['userId' => $userId, 'animeId' => $animeId]);

        return $qb->getQuery()->getOneOrNullResult();
    }

//    public function getAnimeById($id)
//    {
//        $qb = $this->createQueryBuilder('l')
//
//            ->where('l.anime = :animeId')
//            ->setParameter('animeId', $id);
//
//        return $qb->getQuery()->getOneOrNullResult();
//    }

    // /**
    //  * @return Library[] Returns an array of Library objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Library
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
