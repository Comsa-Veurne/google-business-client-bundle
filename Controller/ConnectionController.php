<?php
namespace Cirykpopeye\GoogleBusinessClient\Controller;


use Cirykpopeye\GoogleBusinessClient\Entity\Location;
use Cirykpopeye\GoogleBusinessClient\Entity\LocationPeriod;
use Cirykpopeye\GoogleBusinessClient\Entity\Review;
use Cirykpopeye\GoogleBusinessClient\Manager\Connection;
use Doctrine\ORM\EntityManagerInterface;
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
        $supported_languages = array(
            'en',
            'fr',
            'de',
            'nl'
        );

        $wordList['fr'] = array ('c\'est', 'bien', 'atat', 'rapide', 'ultra rapide', 'lire', 'excellent', 'rien', 'ajouter', 'actif', 'station service', 'sympa', 'bon', 'qualité', 'personne', 'prix', 'propre', 'magasin', 'être', 'avoir', 'je', 'de', 'ne', 'pas', 'le', 'la', 'tu', 'vous', 'il', 'et', 'à', 'un', 'l\'', 'qui', 'aller', 'les', 'en', 'ça', 'faire', 'tout', 'on', 'que', 'ce', 'une', 'mes', 'bonjour', 'mes', 'des', 'se', 'pouvoir', 'vouloir', 'dire', 'mon', 'travail', 'revenir');
        $wordList['en'] = array ('the', 'be', 'to', 'of', 'and', 'a', 'in',
            'that', 'have', 'I', 'it', 'for', 'not', 'on', 'with', 'he', 'good', 'staff',
            'as', 'you', 'do', 'at', 'fuel', 'always', 'stop', 'up', 'as', 'cheapest', 'super');
        $wordList['de'] = ['das', 'ist', 'du', 'ich', 'nicht', 'die', 'es', 'und', 'Sie', 'der', 'was', 'wir', 'zu', 'ein', 'er', 'in', 'sie', 'mir', 'mit', 'ja', 'wie', 'den', 'auf', 'mich', 'dass', 'so', 'hier', 'eine', 'wenn', 'hat', 'all'];
        $wordList['nl'] = ['Veel', 'Vriendelijk', 'keuze', 'Gek', 'Systeem', 'betalen', 'pinpas', 'goedkoop', 'tankstation', 'dicht', 'grens', 'druk', 'goedkoper', 'goedkoop', 'bij'];

        // clean out the input string - note we don't have any non-ASCII
        // characters in the word lists... change this if it is not the
        // case in your language wordlists!
        $text = preg_replace("/[^A-Za-z]/", ' ', $text);
        // count the occurrences of the most frequent words
        foreach ($supported_languages as $language) {
            $counter[$language]=0;
        }

        foreach ($supported_languages as $language) {
            if (isset($wordList[$language])) {
                foreach ($wordList[$language] as $word) {
                    $counter[$language] = $counter[$language] +
                        substr_count(' ' . strtoupper($text) . ' ', ' ' . strtoupper($word) . ' ');
                }
            }
        }

        // get max counter value
        // from http://stackoverflow.com/a/1461363
        $max = max($counter);
        $maxs = array_keys($counter, $max);
        // if there are two winners - fall back to default!
        if (count($maxs) == 1) {
            $winner = $maxs[0];
            $second = 0;
            // get runner-up (second place)
            foreach ($supported_languages as $language) {
                if ($language <> $winner) {
                    if ($counter[$language]>$second) {
                        $second = $counter[$language];
                    }
                }
            }

            return $winner;
        } elseif (count($maxs) == 2) {
            return 'eq';
        }
        return $default;
    }

    private function filterComment(&$comment)
    {
        if (strpos($comment, '(Translated by Google)') === false) return;

        //-- It's translated by Google, fetch the original
        $originalStartsAt = strpos($comment, '(Original)') + strlen('(Original)');
        $comment = substr($comment, $originalStartsAt);
    }

    public function callbackAction()
    {
        $locations = $this->em->getRepository(Location::class)->findAll();
        foreach ($locations as $location) {
            $reviews = $this->connection->getReviews($location->getLocationId());
            foreach ($reviews as $review) {
                //-- Get original text (Strip all the other stuff)
                $this->filterComment($review->comment);
                $locale = $this->getTextLocale($review->comment, null);

                /** @var Review $reviewEntity */
                $reviewEntity = $this->em->getRepository(Review::class)->findOneBy([
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
                foreach ($this->em->getRepository(LocationPeriod::class)->findBy(['location' => $location]) as $locationPeriod) {
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
