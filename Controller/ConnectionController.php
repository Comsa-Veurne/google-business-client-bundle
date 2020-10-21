<?php
namespace Cirykpopeye\GoogleBusinessClient\Controller;


use Cirykpopeye\GoogleBusinessClient\Entity\LocationPeriod;
use Cirykpopeye\GoogleBusinessClient\Entity\Review;
use Cirykpopeye\GoogleBusinessClient\Interfaces\LocationInterface;
use Cirykpopeye\GoogleBusinessClient\Interfaces\LocationPeriodInterface;
use Cirykpopeye\GoogleBusinessClient\Interfaces\ReviewInterface;
use Cirykpopeye\GoogleBusinessClient\Manager\Connection;
use Doctrine\ORM\EntityManagerInterface;
use LanguageDetection\Language;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class ConnectionController extends Controller
{
    /** @var Connection */
    private $connection;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestStack $requestStack
     */
    private $requestStack;

    /**
     * ConnectionController constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->connection = $connection;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function getTextLocale($text, $default) {
        if (empty($text)) {
            return null;
        }

        $ld = new Language;
        $l = $ld
            ->detect($text)
            ->bestResults()
            ->close();

        $l = array_keys($l);
        if (empty($l)) {
            return null;
        }

        return $l[0];
    }

    private function filterComment(&$comment)
    {
        $translatedByGoogleStartsAt = strpos($comment, '(Translated by Google)');
        if ($translatedByGoogleStartsAt === false) return;

        //-- It's translated by Google, fetch the original
        $originalStartsAt = strpos($comment, '(Original)');
        if ($originalStartsAt !== false) {
            $originalStartsAt += strlen('(Original)');
        }

        if ($originalStartsAt === false) {
            //-- Take everything in front of translated by google
            $comment = trim(substr($comment, 0, $translatedByGoogleStartsAt));
        } else {
            $comment = trim(substr($comment, $originalStartsAt));
        }
    }

    public function callbackAction()
    {
        $locations = $this->em->getRepository(LocationInterface::class)->findAll();
        foreach ($locations as $location) {
            $reviews = $this->connection->getReviews($location->getLocationId());
            foreach ($reviews as $review) {
                //-- Get original text (Strip all the other stuff)
                $this->filterComment($review->comment);
                $locale = $this->getTextLocale($review->comment, null);

                /** @var ReviewInterface $reviewEntity */
                $reviewEntity = $this->em->getRepository(ReviewInterface::class)->findOneBy([
                    'reviewId' => $review->reviewId
                ]);

                if (!$reviewEntity) {
                    if (!$review->reviewer->displayName) {
                        continue;
                    }
                    $newReview = new Review(
                        $review->comment,
                        $review->reviewId,
                        $review->starRating,
                        $review->reviewer->displayName,
                        $review->reviewer->profilePhotoUrl,
                        new \DateTime($review->createTime),
                        new \DateTime($review->updateTime),
                        $location,
                        $locale
                    );
                    $this->em->persist($newReview);
                } else {
                    $reviewEntity->setLocale($locale);
                    $reviewEntity->setComment($review->comment);
                    $reviewEntity->setStarRating($review->starRating);
                    $reviewEntity->setReviewer($review->reviewer->displayName);
                    $reviewEntity->setProfilePhoto($review->reviewer->profilePhotoUrl);
                }
            }

            $locationResponse = $this->connection->getLocation($location->getLocationId());
            $periods = $locationResponse->regularHours->periods;

            if ($periods) {
                //-- Remove all existing periods
                foreach ($this->em->getRepository(LocationPeriodInterface::class)->findBy(['location' => $location]) as $locationPeriod) {
                    $this->em->remove($locationPeriod);
                }
                $this->em->flush();
            }
            foreach ($periods as $period) {
                $periodEntity = new LocationPeriod();
                $periodEntity->setCloseDay($period->closeDay);

                $openDateTime = new \DateTime();
                $closeDateTime = new \DateTime();
                $closeTime = explode(':', $period->closeTime);
                $openTime = explode(':', $period->openTime);

                $periodEntity->setCloseTime(($closeDateTime->setTime($closeTime[0], $closeTime[1], 0, 0)));
                $periodEntity->setOpenDay($period->openDay);
                $periodEntity->setOpenTime(($openDateTime->setTime($openTime[0], $openTime[1], 0, 0)));
                $periodEntity->setLocation(
                    $location
                );
                $this->em->persist($periodEntity);
            }
            $this->em->flush();
        }
        return $this->connection->updateLastUpdated();
    }

    public function accountsAction()
    {
        return new JsonResponse($this->connection->getAccounts());
    }

    public function locationsAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request->get('name')) {
            throw new ParameterNotFoundException('name');
        }
        return $this->connection->getLocations($request->get('name'));
    }
}
