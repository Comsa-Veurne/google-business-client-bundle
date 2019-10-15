<?php
/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 2019-03-25
 * Time: 11:47
 */

namespace Cirykpopeye\GoogleBusinessClient\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="google_accounts")
 */
class Account
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Cirykpopeye\GoogleBusinessClient\Entity\Location", mappedBy="account")
     */
    private $locations;

    /**
     * @ORM\Column(type="string")
     */
    private $accountId;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

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
