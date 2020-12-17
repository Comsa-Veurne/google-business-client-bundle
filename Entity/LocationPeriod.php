<?php
/**
 * Created by PhpStorm.
 * User: Comsa
 * Date: 2019-10-14
 * Time: 13:57
 */

namespace Comsa\GoogleBusinessClient\Entity;


use Comsa\GoogleBusinessClient\Interfaces\LocationPeriodInterface;
use Doctrine\ORM\Mapping as ORM;

class LocationPeriod implements LocationPeriodInterface
{
    protected $id;
    protected $location;
    protected $openDay;
    protected $openTime;
    protected $closeDay;
    protected $closeTime;

    public function getId()
    {
        return $this->id;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function getOpenDay()
    {
        return $this->openDay;
    }

    public function setOpenDay($openDay)
    {
        $this->openDay = $openDay;
    }

    public function getOpenTime()
    {
        return $this->openTime;
    }

    public function setOpenTime($openTime)
    {
        $this->openTime = $openTime;
    }

    public function getCloseDay()
    {
        return $this->closeDay;
    }

    public function setCloseDay($closeDay)
    {
        $this->closeDay = $closeDay;
    }

    public function getCloseTime()
    {
        return $this->closeTime;
    }

    public function setCloseTime($closeTime)
    {
        $this->closeTime = $closeTime;
    }
}
