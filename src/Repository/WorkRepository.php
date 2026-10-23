<?php

namespace App\Repository;

use App\Entity\Work;
use App\Enum\WorkType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Work>
 *
 * @method Work|null find($id, $lockMode = null, $lockVersion = null)
 * @method Work|null findOneBy(array $criteria, array $orderBy = null)
 * @method Work[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Work::class);
    }

    public function save(Work $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Work $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Work[] Returns an array of Work objects
     */
    public function findActive(?WorkType $type = null): array
    {
        $qb = $this->createQueryBuilder('w')
            ->where('w.active = :active')
            ->setParameter('active', true)
            ->orderBy('w.price', 'ASC');

        if ($type !== null) {
            $qb->andWhere('w.type = :type')
                ->setParameter('type', $type);
        }

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Active works by position, works without position last, then by name.
     *
     * @return Work[]
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('w')
            ->addSelect('CASE WHEN w.position IS NULL THEN 1 ELSE 0 END AS HIDDEN positionIsNull')
            ->where('w.active = :active')
            ->setParameter('active', true)
            ->orderBy('positionIsNull', 'ASC')
            ->addOrderBy('w.position', 'ASC')
            ->addOrderBy('w.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Work[] Returns an array of Work objects
     */
    public function findAllWithNameAndSlug(): array
    {
        return $this->createQueryBuilder('w')
            ->select('w.id, w.name, w.slug')
            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /**
     * @return Work[] Returns an array of Work objects
     */
    public function findOne($slug): ?Work
    {
        return $this->createQueryBuilder('w')
            ->orderBy('w.id', 'ASC')
            ->andWhere('w.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult()
        ;
    }

/// //    /**
//     * @return Work[] Returns an array of Work objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Work
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
