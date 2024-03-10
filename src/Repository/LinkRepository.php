<?php

namespace App\Repository;

use App\Entity\Link;
use Base62\Base62;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Link>
 *
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    public function save(Link $shortLink): void
    {
        $this->getEntityManager()->persist($shortLink);
        $this->getEntityManager()->flush();
    }

    public function findByShortUrl($shortUrl): ?Link
    {
        $base62 = new Base62();
        $id = $base62->decode($shortUrl) - 1000;

        return $this->find($id);
    }

    public function findOneByUrl($url): ?Link
    {
        return $this->findOneBy(['url' => $url]);
    }

    public function incrementReadCount(Link $shortLink): void
    {
        $this->createQueryBuilder('s')
            ->update(Link::class, 's')
            ->set('s.readCount', 's.readCount + 1')
            ->where('s.id = :id')
            ->setParameter('id', $shortLink->getId())
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return ShortLinks[] Returns an array of ShortLinks objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ShortLinks
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
