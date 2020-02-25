<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 2019-03-25
 * Time: 11:51
 */

namespace Cirykpopeye\GoogleBusinessClient\Entity;


use Cirykpopeye\GoogleBusinessClient\Interfaces\LocationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class Location implements LocationInterface
{
    protected $id;
    protected $reviews;
    protected $locationId;
    protected $name;
    protected $account;
    protected $periods;

    /**
     * Location constructor.
     * @param $locationId
     * @param $name
     * @param $account
     */
    public function __construct($locationId, $name, Account $account)
    {
        $this->reviews = new ArrayCollection();

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
}
