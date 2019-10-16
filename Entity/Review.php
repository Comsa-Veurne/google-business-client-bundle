<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 2019-03-25
 * Time: 10:59
 */

namespace Cirykpopeye\GoogleBusinessClient\Entity;

use Cirykpopeye\GoogleBusinessClient\Model\LocationInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Review
 * @package Cirykpopeye\GoogleBusinessClient\Entity
 * @ORM\Entity(repositoryClass="Cirykpopeye\GoogleBusinessClient\Repository\ReviewRepository")
 * @ORM\Table(name="google_reviews")
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Cirykpopeye\GoogleBusinessClient\Model\LocationInterface", inversedBy="reviews")
     */
    private $location;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string")
     */
    private $reviewId;

    /**
     * @ORM\Column(type="string")
     */
    private $starRating;

    /**
     * @ORM\Column(type="string")
     */
    private $reviewer;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $profilePhoto;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * Review constructor.
     * @param $comment
     * @param $reviewId
     * @param $starRating
     * @param $reviewer
     * @param $profilePhoto
     * @param $createdOn
     * @param $updatedAt
     */
    public function __construct($comment, $reviewId, $starRating, $reviewer, $profilePhoto, $createdOn, $updatedAt, LocationInterface $location, $locale)
    {
        $this->comment = $comment;
        $this->reviewId = $reviewId;
        $this->starRating = $starRating;
        $this->reviewer = $reviewer;
        $this->profilePhoto = $profilePhoto;
        $this->createdOn = $createdOn;
        $this->updatedAt = $updatedAt;
        $this->location = $location;
        $this->locale = $locale;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getReviewId()
    {
        return $this->reviewId;
    }

    public function getStarRating()
    {
        return $this->starRating;
    }

    public function getReviewer()
    {
        return $this->reviewer;
    }

    public function getProfilePhoto()
    {
        return $this->profilePhoto;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function setStarRating($starRating)
    {
        $this->starRating = $starRating;
    }

    public function setReviewer($reviewer)
    {
        $this->reviewer = $reviewer;
    }

    public function setProfilePhoto($profilePhoto)
    {
        $this->profilePhoto = $profilePhoto;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
