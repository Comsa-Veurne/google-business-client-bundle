<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 2019-10-14
 * Time: 13:57
 */

namespace Cirykpopeye\GoogleBusinessClient\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="google_location_period")
 */
class LocationPeriod
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Cirykpopeye\GoogleBusinessClient\Model\LocationInterface", inversedBy="periods")
     */
    private $location;

    /**
     * @ORM\Column(type="string")
     */
    private $openDay;

    /**
     * @ORM\Column(type="time")
     */
    private $openTime;

    /**
     * @ORM\Column(type="string")
     */
    private $closeDay;

    /**
     * @ORM\Column(type="time")
     */
    private $closeTime;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getOpenDay()
    {
        return $this->openDay;
    }

    /**
     * @param mixed $openDay
     */
    public function setOpenDay($openDay)
    {
        $this->openDay = $openDay;
    }

    /**
     * @return mixed
     */
    public function getOpenTime()
    {
        return $this->openTime;
    }

    /**
     * @param mixed $openTime
     */
    public function setOpenTime($openTime)
    {
        $this->openTime = $openTime;
    }

    /**
     * @return mixed
     */
    public function getCloseDay()
    {
        return $this->closeDay;
    }

    /**
     * @param mixed $closeDay
     */
    public function setCloseDay($closeDay)
    {
        $this->closeDay = $closeDay;
    }

    /**
     * @return mixed
     */
    public function getCloseTime()
    {
        return $this->closeTime;
    }

    /**
     * @param mixed $closeTime
     */
    public function setCloseTime($closeTime)
    {
        $this->closeTime = $closeTime;
    }
}
