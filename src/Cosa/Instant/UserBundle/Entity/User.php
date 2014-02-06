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
}
