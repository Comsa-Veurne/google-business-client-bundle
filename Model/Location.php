<?php
namespace Cirykpopeye\GoogleBusinessClient\Model;

use Doctrine\ORM\Mapping as ORM;
use Cirykpopeye\GoogleBusinessClient\Entity\Account;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Location
 * @package Cirykpopeye\GoogleBusinessClient\Model
 * @ORM\MappedSuperclass()
 */
abstract class Location implements LocationInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Cirykpopeye\GoogleBusinessClient\Entity\Review", mappedBy="location")
     */
    protected $reviews;

    /**
     * @ORM\OneToMany(targetEntity="Cirykpopeye\GoogleBusinessClient\Entity\LocationPeriod", mappedBy="location")
     */
    protected $periods;

    /**
     * @ORM\Column(type="string")
     */
    protected $locationId;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Cirykpopeye\GoogleBusinessClient\Entity\Account", inversedBy="locations")
     */
    protected $account;

    /**
     * Location constructor.
     * @param $locationId
     * @param $name
     * @param $account
     */
    public function __construct($locationId, $name, Account $account)
    {
        $this->reviews = new ArrayCollection();
        $this->periods = new ArrayCollection();

        $this->locationId = $locationId;
        $this->name = $name;
        $this->account = $account;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLocationId()
    {
        return $this->locationId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function getReviews()
    {
        return $this->reviews;
    }

    public function getPeriods()
    {
        return $this->periods;
    }
}
