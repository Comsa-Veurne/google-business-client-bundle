<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 2019-04-18
 * Time: 09:17
 */

namespace Cirykpopeye\GoogleBusinessClient\Repository;


use Cirykpopeye\GoogleBusinessClient\Entity\Location;
use Doctrine\ORM\EntityRepository;

class ReviewRepository extends EntityRepository
{
    public function findAllForLocale($locale, $limit = 30, $dynamic = false)
    {
        $qb = $this->createQueryBuilder('i')
            ->where('i.locale = :locale OR i.comment IS NULL')
            ->andWhere('i.starRating = \'FOUR\' OR i.starRating = \'FIVE\'')
            ->andWhere('i.comment IS NOT NULL')
            ->setParameter('locale', (string) $locale)
            ->orderBy('i.createdOn', 'DESC')
            ->setMaxResults($limit);

        if (!$dynamic) {
            return $qb
                ->getQuery()
                ->getResult();
        }

        return $qb;
    }

    public function findAllForLocationAndLocale(Location $location, $locale, $limit = 30)
    {
        $qb = $this->findAllForLocale($locale, $limit, true);
        $qb->andWhere('i.location = :location')
            ->setParameter('location', $location);
        return $qb->getQuery()->getResult();
    }
}
