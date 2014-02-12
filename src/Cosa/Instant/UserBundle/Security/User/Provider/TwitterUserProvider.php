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
use Doctrine\ORM\EntityManager;

class TwitterUserProvider implements UserProviderInterface
{
    /** 
     * @var \Twitter
     */
    protected $twitter_oauth;
    protected $userManager;
    protected $validator;
    protected $session;
    protected $em;

    public function __construct(TwitterOAuth $twitter_oauth, UserManager $userManager,Validator $validator, Session $session, EntityManager $entityManager)
    {   
        $this->twitter_oauth = $twitter_oauth;
        $this->userManager = $userManager;
        $this->validator = $validator;
        $this->session = $session;
        $this->em = $entityManager;
    }   

    public function updateUser($user)
    {
        $this->em->persist($user);
        $this->em->flush();
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

    public function loadUserByUsername($username,$updateInfo=true)
    {
        //$user = $this->findUserByTwitterID($username);
        //echo "findUserByUsername($username)";var_dump(debug_backtrace());exit;
        $user = $this->findUserByUsername($username);

        $this->twitter_oauth->setOAuthToken($this->session->get('access_token'), $this->session->get('access_token_secret'));
        if($updateInfo){
          try {
            $info = $this->twitter_oauth->get('account/verify_credentials');
          } catch (Exception $e) {
            $info = null;
          }

          if (!empty($info)) {
            $updated = false;
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
                $user->setCreatedAt(new \Datetime());
            }
            if($user->getTwitterAccessToken()!=$this->session->get('access_token')){
              $user->setTwitterAccessToken($this->session->get('access_token'));
              $updated = true;
            }
            if($user->getTwitterAccessTokenSecret()!=$this->session->get('access_token_secret')){
              $user->setTwitterAccessTokenSecret($this->session->get('access_token_secret'));
              $updated = true;
            }
            if(!isset($info->name)){var_dump($info);exit;}
            if($user->getTwitterRealname()!=$info->name){
              $user->setTwitterRealname($info->name);
              $updated = true;
            }
            if($user->getProfileImageUrl()!=$info->profile_image_url){
              $user->setProfileImageUrl($info->profile_image_url);
              $updated = true;
            }
            if($user->getTwitterLocation()!=$info->location){
              $user->setTwitterLocation($info->location);
              $updated = true;
            }
            if($user->getLang()!=$info->lang){
              $user->setLang($info->lang);
              $updated = true;
            }

            //$user->setLoginCount($user->getLoginCount()+1);
            if($updated){
              $user->setUpdatedAt(new \Datetime());
              $this->userManager->updateUser($user,true);
            }
          }
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

        return $this->loadUserByUsername($user->getUsername(),false);//TwitterID());
    }
}
