<?php

namespace Cosa\Instant\TimelineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Keyword
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $keyword;

    /**
     * @ORM\ManyToOne(targetEntity="Cosa\Instant\TimelineBundle\Entity\Instant", cascade={"remove","persist"})
     */
    private $instant;

    /**
     * Set keyword
     *
     * @param string $keyword
     * @return Keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword
     *
     * @return string 
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set instant
     *
     * @param \Cosa\Instant\TimelineBundle\Entity\Instant $instant
     * @return Keyword
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
