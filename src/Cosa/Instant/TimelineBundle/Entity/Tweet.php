<?php

namespace Cosa\Instant\TimelineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Tweet
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $twitter_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $screen_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profile_image_url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $media_url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $is_rt;

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
     * Set twitter_id
     *
     * @param string $twitterId
     * @return Tweet
     */
    public function setTwitterId($twitterId)
    {
        $this->twitter_id = $twitterId;

        return $this;
    }

    /**
     * Get twitter_id
     *
     * @return string 
     */
    public function getTwitterId()
    {
        return $this->twitter_id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Tweet
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Tweet
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set screen_name
     *
     * @param string $screenName
     * @return Tweet
     */
    public function setScreenName($screenName)
    {
        $this->screen_name = $screenName;

        return $this;
    }

    /**
     * Get screen_name
     *
     * @return string 
     */
    public function getScreenName()
    {
        return $this->screen_name;
    }

    /**
     * Set user_id
     *
     * @param string $userId
     * @return Tweet
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return string 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set profile_image_url
     *
     * @param string $profileImageUrl
     * @return Tweet
     */
    public function setProfileImageUrl($profileImageUrl)
    {
        $this->profile_image_url = $profileImageUrl;

        return $this;
    }

    /**
     * Get profile_image_url
     *
     * @return string 
     */
    public function getProfileImageUrl()
    {
        return $this->profile_image_url;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Tweet
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set media_url
     *
     * @param string $mediaUrl
     * @return Tweet
     */
    public function setMediaUrl($mediaUrl)
    {
        $this->media_url = $mediaUrl;

        return $this;
    }

    /**
     * Get media_url
     *
     * @return string 
     */
    public function getMediaUrl()
    {
        return $this->media_url;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Tweet
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
     * Set is_rt
     *
     * @param boolean $isRt
     * @return Tweet
     */
    public function setIsRt($isRt)
    {
        $this->is_rt = $isRt;

        return $this;
    }

    /**
     * Get is_rt
     *
     * @return boolean 
     */
    public function getIsRt()
    {
        return $this->is_rt;
    }
}
