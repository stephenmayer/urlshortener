<?php

namespace App\Repository;

use App\Entity\ShortLinks;
use Base62\Base62;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShortLinks>
 *
 * @method ShortLinks|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortLinks|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortLinks[]    findAll()
 * @method ShortLinks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortLinksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortLinks::class);
    }

    public function save(ShortLinks $shortLink): void
    {
        $this->getEntityManager()->persist($shortLink);
        $this->getEntityManager()->flush();
    }

    public function findByShortUrl($shortUrl): ?ShortLinks
    {
        $base62 = new Base62();
        $id = $base62->decode($shortUrl);

        return $this->find($id);
    }

    public function findOneByUrl($url): ?ShortLinks
    {
        return $this->findOneBy(['url' => $url]);
    }

    public function incrementReadCount(ShortLinks $shortLink): void
    {
        $shortLink->setReadCount($shortLink->getReadCount() + 1);
        $this->save($shortLink);
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
