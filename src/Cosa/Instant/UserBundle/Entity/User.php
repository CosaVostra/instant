<?php

namespace Cosa\Instant\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $twitterID;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $twitter_username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $twitter_access_token;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $twitter_access_token_secret;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $profile_image_url;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $twitter_realname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $twitter_location;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $optin;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="integer")
     */
    protected $login_count;

    /**
     * @ORM\Column(type="string", length=4)
     */
    protected $lang;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

        /**
         * Set twitterID
         *
         * @param string $twitterID
         */
        public function setTwitterID($twitterID)
        {
            $this->twitterID = $twitterID;
            $this->setUsername($twitterID);
            $this->salt = '';
        }

        /**
         * Get twitterID
         *
         * @return string 
         */
        public function getTwitterID()
        {
            return $this->twitterID;
        }

        /**
         * Set twitter_username
         *
         * @param string $twitterUsername
         */
        public function setTwitterUsername($twitterUsername)
        {
            $this->twitter_username = $twitterUsername;
        }

        /**
         * Get twitter_username
         *
         * @return string 
         */
        public function getTwitterUsername()
        {
            return $this->twitter_username;
        }

      /**
         * Set twitter_username
         *
         * @param string $twitterUsername
         */
        public function setTwitterAccessToken($twitterAccessToken)
        {
            $this->twitter_access_token = $twitterAccessToken;
        }

        /**
         * Get twitter_access_token
         *
         * @return string 
         */
        public function getTwitterAccessToken()
        {
            return $this->twitter_access_token;
        }

        /**
         * Set twitter_access_token_secret
         *
         * @param string $twitterAccessTokenSecret
         */
        public function setTwitterAccessTokenSecret($twitterAccessTokenSecret)
        {
            $this->twitter_access_token_secret = $twitterAccessTokenSecret;
        }

        /**
         * Get twitter_access_token_secret
         *
         * @return string 
         */
        public function getTwitterAccessTokenSecret()
        {
            return $this->twitter_access_token_secret;
        }

    /**
     * Set profile_image_url
     *
     * @param string $profileImageUrl
     * @return User
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
     * Set twitter_realname
     *
     * @param string $twitterRealname
     * @return User
     */
    public function setTwitterRealname($twitterRealname)
    {
        $this->twitter_realname = $twitterRealname;

        return $this;
    }

    /**
     * Get twitter_realname
     *
     * @return string 
     */
    public function getTwitterRealname()
    {
        return $this->twitter_realname;
    }

    /**
     * Set twitter_location
     *
     * @param string $twitterLocation
     * @return User
     */
    public function setTwitterLocation($twitterLocation)
    {
        $this->twitter_location = $twitterLocation;

        return $this;
    }

    /**
     * Get twitter_location
     *
     * @return string 
     */
    public function getTwitterLocation()
    {
        return $this->twitter_location;
    }

    /**
     * Set optin
     *
     * @param string $optin
     * @return User
     */
    public function setOptin($optin)
    {
        $this->optin = $optin;

        return $this;
    }

    /**
     * Get optin
     *
     * @return string 
     */
    public function getOptin()
    {
        return $this->optin;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return User
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
     * @return User
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
     * Set login_count
     *
     * @param integer $loginCount
     * @return User
     */
    public function setLoginCount($loginCount)
    {
        $this->login_count = $loginCount;

        return $this;
    }

    /**
     * Get login_count
     *
     * @return integer 
     */
    public function getLoginCount()
    {
        return $this->login_count;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return User
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
}
