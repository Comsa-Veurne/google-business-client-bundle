<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 2019-04-18
 * Time: 09:17
 */

namespace Cirykpopeye\GoogleBusinessClient\Repository;


use Cirykpopeye\GoogleBusinessClient\Interfaces\LocationInterface;
use Cirykpopeye\GoogleBusinessClient\Interfaces\ReviewRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class ReviewRepository extends EntityRepository implements ReviewRepositoryInterface
{
    public function findAllForLocale($locale, $limit = 30, $dynamic = false)
    {
        $qb = $this->createQueryBuilder('i')
            ->where('i.locale = :locale OR i.comment IS NULL')
            ->andWhere('i.starRating = \'FOUR\' OR i.starRating = \'FIVE\'')
            ->andWhere('i.comment IS NOT NULL')
            ->andWhere('i.comment NOT LIKE :free')
            ->setParameter('locale', (string) $locale)
            ->setParameter('free', (string) $locale === 'fr' ? '%gratuit%' : '%free%')
            ->orderBy('i.createdOn', 'DESC')
            ->setMaxResults($limit);

        if (!$dynamic) {
            return $qb
                ->getQuery()
                ->getResult();
        }

        return $qb;
    }

    public function findAllForLocationAndLocale(LocationInterface $location, $locale, $limit = 30)
    {
        $qb = $this->findAllForLocale($locale, $limit, true);
        $qb->andWhere('i.location = :location')
            ->setParameter('location', $location);
        return $qb->getQuery()->getResult();
    }
}
