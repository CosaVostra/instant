<?php

namespace Cosa\Instant\TimelineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Cosa\Instant\TimelineBundle\Entity\InstantRepository")
 */
class Instant
{
    /**
     * @ORM\ManyToMany(targetEntity="Cosa\Instant\TimelineBundle\Entity\Tweet", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $tweets;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $finish_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default":"draft"})
     */
    private $status = "draft";

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default":0})
     */
    private $nb_views = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_view;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $lang;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default":"Dear @EXPERT, ....."})
     */
    private $message_type = "Dear @EXPERT, .....";

    /**
     * @ORM\ManyToOne(targetEntity="Cosa\Instant\UserBundle\Entity\User")
     */
    private $user;


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
     * Set title
     *
     * @param string $title
     * @return Instant
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Instant
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Instant
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Instant
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set finish_at
     *
     * @param \DateTime $finishAt
     * @return Instant
     */
    public function setFinishAt($finishAt)
    {
        $this->finish_at = $finishAt;

        return $this;
    }

    /**
     * Get finish_at
     *
     * @return \DateTime 
     */
    public function getFinishAt()
    {
        return $this->finish_at;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Instant
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set nb_views
     *
     * @param integer $nbViews
     * @return Instant
     */
    public function setNbViews($nbViews)
    {
        $this->nb_views = $nbViews;

        return $this;
    }

    /**
     * Get nb_views
     *
     * @return integer 
     */
    public function getNbViews()
    {
        return $this->nb_views;
    }

    /**
     * Set last_view
     *
     * @param \DateTime $lastView
     * @return Instant
     */
    public function setLastView($lastView)
    {
        $this->last_view = $lastView;

        return $this;
    }

    /**
     * Get last_view
     *
     * @return \DateTime 
     */
    public function getLastView()
    {
        return $this->last_view;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return Instant
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string 
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set message_type
     *
     * @param string $messageType
     * @return Instant
     */
    public function setMessageType($messageType)
    {
        $this->message_type = $messageType;

        return $this;
    }

    /**
     * Get message_type
     *
     * @return string 
     */
    public function getMessageType()
    {
        return $this->message_type;
    }

    /**
     * Set user
     *
     * @param \Cosa\Instant\UserBundle\Entity\User $user
     * @return Instant
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
     * Constructor
     */
    public function __construct()
    {
        $this->tweets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created_at = new \Datetime();
        $this->updated_at = new \Datetime();
        $this->finish_at = new \Datetime();
        $this->finish_at->modify('+5 day');
    }

    /**
     * Add tweets
     *
     * @param \Cosa\Instant\TimelineBundle\Entity\Tweet $tweets
     * @return Instant
     */
    public function addTweet(\Cosa\Instant\TimelineBundle\Entity\Tweet $tweets)
    {
        $this->tweets[] = $tweets;

        return $this;
    }

    /**
     * Remove tweets
     *
     * @param \Cosa\Instant\TimelineBundle\Entity\Tweet $tweets
     */
    public function removeTweet(\Cosa\Instant\TimelineBundle\Entity\Tweet $tweets)
    {
        $this->tweets->removeElement($tweets);
    }

    /**
     * Get tweets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTweets()
    {
        return $this->tweets;
    }
}
