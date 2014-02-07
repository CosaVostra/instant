<?php

namespace Cosa\Instant\TimelineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Timeline
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Cosa\Instant\TimelineBundle\Entity\Instant", cascade={"remove","persist"}, inversedBy="timelines")
     */
    private $instant;

    /**
     * @ORM\ManyToOne(targetEntity="Cosa\Instant\TimelineBundle\Entity\Tweet", cascade={"remove","persist"}, inversedBy="timelines")
     */
    private $tweet;


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
     * Set instant
     *
     * @param \Cosa\Instant\TimelineBundle\Entity\Instant $instant
     * @return Timeline
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
     * Set tweet
     *
     * @param \Cosa\Instant\TimelineBundle\Entity\Tweet $tweet
     * @return Timeline
     */
    public function setTweet(\Cosa\Instant\TimelineBundle\Entity\Tweet $tweet = null)
    {
        $this->tweet = $tweet;

        return $this;
    }

    /**
     * Get tweet
     *
     * @return \Cosa\Instant\TimelineBundle\Entity\Tweet 
     */
    public function getTweet()
    {
        return $this->tweet;
    }
}
