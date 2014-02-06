<?php
// src/Acme/YourBundle/Security/User/Provider/TwitterUserProvider.php


namespace Cosa\Instant\UserBundle\Security\User\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;//JOHAN
use \TwitterOAuth;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Validator\Validator;

class TwitterUserProvider implements UserProviderInterface
{
    /** 
     * @var \Twitter
     */
    protected $twitter_oauth;
    protected $userManager;
    protected $validator;
    protected $session;

    public function __construct(TwitterOAuth $twitter_oauth, UserManager $userManager,Validator $validator, Session $session)
    {   
        $this->twitter_oauth = $twitter_oauth;
        $this->userManager = $userManager;
        $this->validator = $validator;
        $this->session = $session;
    }   

    public function supportsClass($class)
    {   
        return $this->userManager->supportsClass($class);
    }   

    public function findUserByTwitterId($twitterID)
    {   
        return $this->userManager->findUserBy(array('twitterID' => $twitterID));
    }

    public function findUserByUsername($username)
    {
        return $this->userManager->findUserBy(array('twitter_username' => $username));
    }

    public function loadUserByUsername($username)
    {
        //$user = $this->findUserByTwitterID($username);
        //echo "findUserByUsername($username)";var_dump(debug_backtrace());exit;
        $user = $this->findUserByUsername($username);

        $this->twitter_oauth->setOAuthToken($this->session->get('access_token'), $this->session->get('access_token_secret'));
        try {
            $info = $this->twitter_oauth->get('account/verify_credentials');
        } catch (Exception $e) {
            $info = null;
        }
        if (!empty($info)) {
            if (empty($user)) {
                $user = $this->userManager->createUser();
                $user->setEnabled(true);
                $user->setPassword('');
            //    $user->setAlgorithm('');
              $username = $info->screen_name;
              $user->setTwitterID($info->id);
              $user->setTwitterUsername($username);
              $user->setEmail('');
              $user->setUsername($username);
            }

            $user->setTwitterAccessToken($this->session->get('access_token'));
            $user->setTwitterAccessTokenSecret($this->session->get('access_token_secret'));
            $this->userManager->updateUser($user,true);
        }

        if (empty($user)) {
            throw new UsernameNotFoundException('The user is not authenticated on twitter');
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {//var_dump($user);exit;
        if (!$this->supportsClass(get_class($user)) || !$user->getUsername()){//TwitterID()) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());//TwitterID());
    }
}
