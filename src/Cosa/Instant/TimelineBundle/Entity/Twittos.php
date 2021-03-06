<?php

namespace Cosa\Instant\TimelineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Cosa\Instant\TimelineBundle\Entity\TwittosRepository")
 */
class Twittos
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $alerted;

    /**
     * @ORM\ManyToOne(targetEntity="Cosa\Instant\UserBundle\Entity\User", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Cosa\Instant\TimelineBundle\Entity\Instant", cascade={"persist"})
     */
    private $instant;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Twittos
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set user
     *
     * @param \Cosa\Instant\UserBundle\Entity\User $user
     * @return Twittos
     */
    public function setUser(\Cosa\Instant\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Cosa\Instant\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set instant
     *
     * @param \Cosa\Instant\TimelineBundle\Entity\Instant $instant
     * @return Twittos
     */
    public function setInstant(\Cosa\Instant\TimelineBundle\Entity\Instant $instant = null)
    {
        $this->instant = $instant;

        return $this;
    }

    /**
     * Get instant
     *
     * @return \Cosa\Instant\TimelineBundle\Entity\Instant 
     */
    public function getInstant()
    {
        return $this->instant;
    }

    /**
     * Set alerted
     *
     * @param boolean $alerted
     * @return Twittos
     */
    public function setAlerted($alerted)
    {
        $this->alerted = $alerted;

        return $this;
    }

    /**
     * Get alerted
     *
     * @return boolean 
     */
    public function getAlerted()
    {
        return $this->alerted;
    }
}
