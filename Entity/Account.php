<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 2019-03-25
 * Time: 11:47
 */

namespace Cirykpopeye\GoogleBusinessClient\Entity;


use Cirykpopeye\GoogleBusinessClient\Interfaces\AccountInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class Account implements AccountInterface
{
    protected $id;
    protected $locations;
    protected $accountId;
    protected $name;

    /**
     * Account constructor.
     */
    public function __construct(
        $accountId,
        $name
    )
    {
        $this->accountId = $accountId;
        $this->name = $name;
        $this->locations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAccountId()
    {
        return $this->accountId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLocations()
    {
        return $this->locations;
    }
}
