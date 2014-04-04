<?php

namespace Cosa\Instant\TimelineBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Cosa\Instant\TimelineBundle\Entity\Instant;
use Cosa\Instant\TimelineBundle\Form\InstantType;
use Cosa\Instant\TimelineBundle\Entity\Keyword;
use Cosa\Instant\TimelineBundle\Entity\Twittos;
use Cosa\Instant\UserBundle\Entity\User;
use Cosa\Instant\TimelineBundle\Entity\Tweet;

/**
 * Instant controller.
 *
 */
class InstantController extends Controller
{
    /**
     * Lists all Instant entities.
     *
     */
/*    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CosaInstantTimelineBundle:Instant')->findAll();

        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
        }

        return $this->render('CosaInstantTimelineBundle:Instant:index.html.twig', array(
            'entities' => $entities,
            'deleteForms' => $deleteForms,
        ));
    }*/

    /**
     * Creates a new Instant entity.
     *
     */
    public function createAction(Request $request, $username)
    {
        if(($tmp=$this->userEmailIsConfirmed())!==true){
            return $tmp;
        }
        $user = $this->checkUser($username);
        $entity  = new Instant();
        $form = $this->createForm(new InstantType(), $entity);
        $form->bind($request);
        if ($entity->getUser()!=$user){
            throw new AccessDeniedException();
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('instant_edit', array('username' => $username, 'instant_title' => $entity->getUrlTitle())));
        }

        return $this->render('CosaInstantTimelineBundle:Instant:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Instant entity.
     *
     */
    public function newAction()
    {
        if(($tmp=$this->userEmailIsConfirmed())!==true){
            return $tmp;
        }
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $entity = new Instant();
        $entity->setUser($user);
        $last_id = $em->getRepository('CosaInstantTimelineBundle:Instant')->getLastId();
        if(empty($last_id) || !isset($last_id['id']))
          $last_id['id'] = 1;
        $entity->setTitle("Draft - ".($last_id['id']+1));
        $entity->setUrlTitle(urlencode("Draft - ".($last_id['id']+1)));
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('instant_edit',array('username'=>$user->getTwitterUsername(),'instant_title'=>$entity->getUrlTitle())));

      /*  $form   = $this->createForm(new InstantType(), $entity);

        return $this->render('CosaInstantTimelineBundle:Instant:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'user'   => $user,
        ));*/
    }

    /**
     * Finds and displays a Instant entity.
     *
     */
/*    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Instant entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CosaInstantTimelineBundle:Instant:show.html.twig', array(
            'entity'      => $entity,
            'tweets'      => $entity->getTweets(),
            'delete_form' => $deleteForm->createView(),        ));
    }*/

    public function nbNewTweetsAction(Request $request,$instant_id)
    {
      try{
        $em = $this->getDoctrine()->getManager();
        $instant = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($instant_id);
        if (!$instant) {
          throw $this->createNotFoundException('Unable to find Instant entity.');
        }
        if($request->request->get('last') && is_numeric($request->request->get('last'))){
          $nb_new = $em->getRepository('CosaInstantTimelineBundle:Instant')->getNbNewTweets($instant_id,$request->request->get('last'));
        }else{
          $nb_new = $em->getRepository('CosaInstantTimelineBundle:Instant')->getNbNewTweets($instant_id);
        }
        return new JsonResponse(array('retour'=>true,'nb'=>$nb_new),200,array('Content-Type', 'application/json'));
      }catch(\Exception $e){
        return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
      }
    }

    public function moreTweetsAction(Request $request,$instant_id)
    {
      $em = $this->getDoctrine()->getManager();
      $instant = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($instant_id);
      if (!$instant) {
        throw $this->createNotFoundException('Unable to find Instant entity.');
      }
      $editable = false;
      if($request->request->get('from') && $request->request->get('from')=='edit'){
        if($this->checkInstant2($instant_id)){
          $editable = true;
        }
      }
      $off = 0;
      $nb = 100;
      $icons = false;
      if($request->request->get('off') && is_numeric($request->request->get('off')))
        $off = $request->request->get('off');
      if($request->request->get('nb') && is_numeric($request->request->get('nb')))
        $nb = $request->request->get('nb');
      if($request->request->get('icons') && ($request->request->get('icons') == 'true'))
        $icons = true;
      $instantWithTweets = $em->getRepository('CosaInstantTimelineBundle:Instant')->getList($instant_id,$off,$nb);
      $tweets = Array();
      if(count($instantWithTweets))
        $tweets = $instantWithTweets[0]->getTweets();
      return $this->render('CosaInstantTimelineBundle:Instant:tweetList.html.twig', array(
            'instant'          => $instant,
            'tweets'           => $tweets,
            'editable'         => $editable,
            'off'              => $off,
            'nb'               => $nb,
            'icons'            => $icons,
        ));
    }

    /**
     * Displays a form to edit an existing Instant entity.
     *
     */
    public function editAction($username,$instant_title)
    {
        if(($tmp=$this->userEmailIsConfirmed())!==true){
            return $tmp;
        }
        $user = $this->checkUser($username);
        $entity = $this->checkInstant($user,$instant_title);
        $em = $this->getDoctrine()->getManager();
        $twittos = $em->getRepository('CosaInstantTimelineBundle:Twittos')->getComplete($entity->getId());
        $twittos_to_alert = $em->getRepository('CosaInstantTimelineBundle:Twittos')->getTwittosToAlert($entity->getId());
        //$keywords = $em->getRepository('CosaInstantTimelineBundle:Keyword')->findByInstant($entity->getId());
        $keywords = $em->getRepository('CosaInstantTimelineBundle:Keyword')->findBy(array('instant' => $entity->getId()), array('id' => 'desc'));
        //$tweets = $entity->getTweets();
        $instantWithTweets = $em->getRepository('CosaInstantTimelineBundle:Instant')->getList($entity->getId(),0,100);
        $tweets = Array();
        if(count($instantWithTweets))
          $tweets = $instantWithTweets[0]->getTweets();
        $editForm = $this->createForm(new InstantType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);
        return $this->render('CosaInstantTimelineBundle:Instant:edit.html.twig', array(
            'entity'           => $entity,
            'edit_form'        => $editForm->createView(),
        //    'delete_form' => $deleteForm->createView(),
            'user'             => $user,
        //    'email_needed'     => ($user->getEmail()=='')?true:false,
            'twittos_to_alert' => (!empty($twittos_to_alert))?$twittos_to_alert:false,
            'twittos'          => $twittos,
            'keywords'          => $keywords,
            'tweets'           => $tweets,
            'off'              => 0,
            'nb'               => 100,

        ));
    }

    private function save(Request $request, $username, $instant_title, $publishForce = false)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->checkUser($username);
        $entity = $this->checkInstant($user,$instant_title);
        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InstantType(), $entity);
        $editForm->bind($request);
        if ($entity->getUser()!=$user){
            throw new AccessDeniedException();
        }
        if ($editForm->isValid()) {
            if($publishForce){
                $entity->setStatus('publish');
            }
            $em->persist($entity);
            $em->flush();
            if ($entity->getTitle()!=$instant_title){
                $instant_title = $entity->getTitle();
            }
            return true;
        }
        return $editForm;
    }

    /**
     * Edits an existing Instant entity.
     *
     */
    public function updateAction(Request $request, $instant_id)
    {
        if(($tmp=$this->userEmailIsConfirmed())!==true){
            return $tmp;
        }
        $instant = $this->checkInstant2($instant_id);
        $instant->setStatus('publish');
        $em = $this->getDoctrine()->getManager();
        $em->persist($instant);
        $em->flush();
        $user = $this->get('security.context')->getToken()->getUser();
        //if($request->request->get('bsubmit')=='publish'){
        //    $editForm = $this->save($request, $user->getTwitterUsername(), $instant->getTitle(), true);
        //    if ($editForm === true) {
              return $this->redirect($this->generateUrl('instant_embed', array('username' => $user->getTwitterUsername(), 'instant_title' => $instant->getUrlTitle())));
        //    }
        //}else{
        //    $editForm = $this->save($request, $username, $instant_title);
        //    if ($editForm === true) {
        //      return $this->redirect($this->generateUrl('instant_edit', array('username' => $username, 'instant_title' => $instant_title)));
        //    }
        //}
        return $this->render('CosaInstantTimelineBundle:Instant:edit.html.twig', array(
            'entity'      => $instant,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
            'user'        => $user,
        ));
    }

    /**
     * Deletes a Instant entity.
     *
     */
    public function deleteAction(Request $request,$username,$instant_title)
    {
        if(($tmp=$this->userEmailIsConfirmed())!==true){
            return $tmp;
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->checkUser($username);
        $entity = $this->checkInstant($user,$instant_title);
        $form = $this->createDeleteForm($entity->getId());
        $form->bind($request);

        if ($form->isValid()) {
            $keywords = $em->getRepository('CosaInstantTimelineBundle:Keyword')->findByInstant($entity->getId());
            foreach ($keywords as $keyword)
            {
                $em->remove($keyword);
            }
            $twittos_list = $em->getRepository('CosaInstantTimelineBundle:Twittos')->findByInstant($entity->getId());
            foreach ($twittos_list as $twittos)
            {
                $em->remove($twittos);
            }
            foreach ($entity->getTweets() as $tweet)
            {
                $entity->removeTweet($tweet);
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * Creates a form to delete a Instant entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function alertTwittosTmpAction(Request $request, $id)
    {
        if(($tmp=$this->userEmailIsConfirmed())!==true){
            return $tmp;
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Instant entity.');
        }

        if($entity->getUser()!=$this->get('security.context')->getToken()->getUser()){
            throw new AccessDeniedException();
        }
        $user = $entity->getUser();
        // On crée le FormBuilder grâce à la méthode du contrôleur
        $formBuilder = $this->createFormBuilder($entity);
        $formBuilder->add('messageType', 'textarea');
        $form = $formBuilder->getForm();
        $twittos = $em->getRepository('CosaInstantTimelineBundle:Twittos')->getTwittosToAlert($entity->getId());
        if ($request->getMethod() == 'POST') {
          $form->bind($request);
          if ($form->isValid()) {
            try{
              $em = $this->getDoctrine()->getManager();
              $em->persist($entity);
              $em->flush();
            }catch(\Exception $e){
              return new JsonResponse(array('retour'=>false),200,array('Content-Type', 'application/json'));
            }
            require_once ('codebird.php');
            \Codebird\Codebird::setConsumerKey($this->container->parameters["fos_twitter.consumer_key"], $this->container->parameters["fos_twitter.consumer_secret"]); // static, see 'Using multiple Codebird instances'
            $cb = new \Codebird\Codebird;
            $cb->setToken($user->getTwitterAccessToken(), $user->getTwitterAccessTokenSecret());
            foreach ($twittos as $twitto) {
              $tuser = $twitto->getUser();
              $message = str_replace('@EXPERT','@'.$tuser->getTwitterUsername(),$entity->getMessageType());
              $message = str_replace('@JOURNALIST','@'.$user->getTwitterUsername(),$message);
              $message = str_replace('@INSTANT',$entity->getTitle(),$message);
              $message = str_replace('@WEBVIEW',$this->get('router')->generate('instant_webview', array('username' => $user->getTwitterUsername(),'instant_title' => $entity->getUrlTitle()),true),$message);
              $reply = $cb->statuses_update('status='.urlencode($message));
              if($reply->httpstatus==200){
                $twitto->setAlerted(true);
                $em->persist($twitto);
                $em->flush();
              }else{
                break;
              }
            }
            return new JsonResponse(array('retour'=>true),200,array('Content-Type', 'application/json'));
          }else{
            return new JsonResponse(array('retour'=>false),200,array('Content-Type', 'application/json'));
          }
        }

        return $this->render('CosaInstantTimelineBundle:Instant:alertTwittos.html.twig', array(
            'form' => $form->createView(),
            'twittos' => $twittos,
        ));
    }
        
   /**
    * check user
    */
    private function checkUser($username)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CosaInstantUserBundle:User')->findOneBy(array('twitter_username'=>$username));
        if (!$user) {
            throw $this->createNotFoundException('This user does not exist');
        } else if ($user != $this->get('security.context')->getToken()->getUser()) {
            throw new AccessDeniedException();
        }
        return $user;
    }

private function checkTweet($tweet_id)
    {
        $em = $this->getDoctrine()->getManager();
        $tweet = $em->getRepository('CosaInstantTimelineBundle:Tweet')->find($tweet_id);
        if (!$tweet) {
            throw $this->createNotFoundException('This tweet does not exist');
        }
        return $tweet;
    }

    private function checkInstant2($id)
    {
        $em = $this->getDoctrine()->getManager();
        $instant = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($id);
        if (!$instant) {
            throw $this->createNotFoundException('This instant does not exist');
        } else if ($instant->getUser()!=$this->get('security.context')->getToken()->getUser()) {
            throw new AccessDeniedException();
        }
        return $instant;
    }

   /**
    * check instant
    */
    private function checkInstant($user,$title)
    {
        $em = $this->getDoctrine()->getManager();
        $instant = $em->getRepository('CosaInstantTimelineBundle:Instant')->findOneBy(array('user'=>$user->getId(),'url_title'=>$title));
        if (!$instant) {
            throw $this->createNotFoundException('This instant does not exist');
        } else if ($instant->getUser()!=$user) {
            throw new AccessDeniedException();
        }
        return $instant;
    }

   /**
    * List Instants from a given user
    *
    * @param string $username The username
    *
    */
    public function userInstantListAction($username, $order)
    {
        if(($tmp=$this->userEmailIsConfirmed())!==true){
            return $tmp;
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->checkUser($username);
        //$entities = $em->getRepository('CosaInstantTimelineBundle:Instant')->findByUser($user->getId());
        $session = $this->getRequest()->getSession();
        $order_session = $session->get('order');
        if (($order === 'undefined') && $order_session)
          $order = $order_session;
        if ($order === 'asc') {
          $entities = $em->getRepository('CosaInstantTimelineBundle:Instant')->findBy(array('user' => $user->getId()), array('created_at' => 'asc'));
          $session->set('order', 'asc');
        } else {
          $entities = $em->getRepository('CosaInstantTimelineBundle:Instant')->findBy(array('user' => $user->getId()), array('created_at' => 'desc'));
          $session->set('order', 'desc');
        }
        $deleteForms = array();
        $nbTwittos = array();
        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
            $nbTwittos[$entity->getId()] = $em->getRepository('CosaInstantTimelineBundle:Twittos')->getNbTwittos($entity->getId());
        }
        return $this->render('CosaInstantTimelineBundle:Instant:userInstantList.html.twig', array(
            'entities' => $entities,
            'deleteForms' => $deleteForms,
            'user' => $user,
            'nbTwittos' => $nbTwittos,
        ));
    }

    /**
    * Show Instant webview code from a given user
    *
    * @param string $username The user name
    * @param string $instant_title The instant title
    */
    public function userInstantWviewAction($username,$instant_title,$webview = true)
    {
        $request = $this->getRequest();
        $request->setLocale($request->getPreferredLanguage(array('en', 'fr')));
//        $this->get('session')->set('_locale', 'de_DE');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CosaInstantUserBundle:User')->findOneBy(array('twitter_username'=>$username));
        if (!$user) {
            throw $this->createNotFoundException('This user does not exist');
        }
        $instant = $em->getRepository('CosaInstantTimelineBundle:Instant')->findOneBy(array('user'=>$user->getId(),'url_title'=>$instant_title));
        if (!$instant) {
            throw $this->createNotFoundException('This instant does not exist');
        }
        $instant->setNbViews($instant->getNbViews()+1);
        $instant->setLastView(new \Datetime());
        $instantWithTweets = $em->getRepository('CosaInstantTimelineBundle:Instant')->getList($instant->getId(),0,100);
        $tweets = Array();
        if(count($instantWithTweets))
          $tweets = $instantWithTweets[0]->getTweets();
        $twittos = $em->getRepository('CosaInstantTimelineBundle:Twittos')->getComplete($instant->getId());
        $em->persist($instant);
        $em->flush();
        return $this->render('CosaInstantTimelineBundle:Instant:userInstantWview.html.twig', array(
            'instant' => $instant,
            'tweets' => $tweets,
            'twittos' => $twittos,
            'off'              => 0,
            'nb'               => 100,
            'webview'          => $webview,
        ));
    }

    /**
    * Show Instant webview code from a given user
    *
    * @param string $username The user name
    * @param string $instant_title The instant title
    */
    public function userInstantWebviewAction($username,$instant_title,$webview = true)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CosaInstantUserBundle:User')->findOneBy(array('twitter_username'=>$username));
        if (!$user) {
            throw $this->createNotFoundException('This user does not exist');
        }
        $instant = $em->getRepository('CosaInstantTimelineBundle:Instant')->findOneBy(array('user'=>$user->getId(),'url_title'=>$instant_title));
        if (!$instant) {
            throw $this->createNotFoundException('This instant does not exist');
        }
        $instant->setNbViews($instant->getNbViews()+1);
        $instant->setLastView(new \Datetime());
        $instantWithTweets = $em->getRepository('CosaInstantTimelineBundle:Instant')->getList($instant->getId(),0,100);
        $tweets = Array();
        if(count($instantWithTweets))
          $tweets = $instantWithTweets[0]->getTweets();
        $twittos = $em->getRepository('CosaInstantTimelineBundle:Twittos')->getComplete($instant->getId());
        $em->persist($instant);
        $em->flush();
        return $this->render('CosaInstantTimelineBundle:Instant:userInstantWebview.html.twig', array(
            'instant' => $instant,
            'tweets' => $tweets,
            'twittos' => $twittos,
            'off'              => 0,
            'nb'               => 100,
            'webview'          => $webview,
        ));
    }

    /**
    * Show Instant webview code from a given user
    *
    * @param string $username The user name
    * @param string $instant_title The instant title
    */
    public function userInstantViewAction($username,$instant_title)
    {
        return $this->userInstantWebviewAction($username,$instant_title,false);
    }

    /**
    * Show Instant preview code from a given user
    *
    * @param string $username The user name
    * @param string $instant_title The instant title
    */
    public function userInstantPreviewAction($instant_id)
    {
        if(($tmp=$this->userEmailIsConfirmed())!==true){
            return $tmp;
        }
        $instant = $this->checkInstant2($instant_id);
        return $this->redirect($this->generateUrl('instant_preview2',array('username'=>$this->get('security.context')->getToken()->getUser()->getTwitterUsername(),'instant_title'=>$instant->getUrlTitle())));
    }

    public function instantPreviewAction($username,$instant_title)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->checkUser($username);
        $instant = $this->checkInstant($user,$instant_title);
        $instantWithTweets = $em->getRepository('CosaInstantTimelineBundle:Instant')->getList($instant->getId(),0,100);
        $tweets = Array();
        if(count($instantWithTweets))
          $tweets = $instantWithTweets[0]->getTweets();
        return $this->render('CosaInstantTimelineBundle:Instant:userInstantPreview.html.twig', array(
            'instant' => $instant,
            'user' => $user,
            'tweets' => $tweets,
            'off'              => 0,
            'nb'               => 100,
        ));
    }

    /**
    * Show Instant embed code from a given user
    *
    * @param string $username The user name
    * @param string $instant_title The instant title
    */
    public function userInstantEmbedAction($username,$instant_title)
    {
        if(($tmp=$this->userEmailIsConfirmed())!==true){
            return $tmp;
        }
        $user = $this->checkUser($username);
        $instant = $this->checkInstant($user,$instant_title);
        return $this->render('CosaInstantTimelineBundle:Instant:userInstantEmbed.html.twig', array(
            'instant' => $instant,
            'user' => $user
        ));
    }

    private function userEmailIsConfirmed()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if($user->getConfirmationToken()!='confirmed'){
            return $this->redirect($this->generateUrl('please_confirm_email',array('username'=>$user->getTwitterUsername())));
        }
        return true;
    }

    public function geoSearchAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        require_once ('codebird.php');
        \Codebird\Codebird::setConsumerKey($this->container->parameters["fos_twitter.consumer_key"], $this->container->parameters["fos_twitter.consumer_secret"]); // static, see 'Using multiple Codebird instances'
        $cb = new \Codebird\Codebird;
        $cb->setToken($user->getTwitterAccessToken(), $user->getTwitterAccessTokenSecret());
        $reply = $cb->geo_search('query='.$request->request->get('q'));
        return new JsonResponse(array('retour'=>true,'datas'=>$reply),200,array('Content-Type', 'application/json'));
    }

    public function twitterSearchAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();
        require_once ('codebird.php');
        \Codebird\Codebird::setConsumerKey($this->container->parameters["fos_twitter.consumer_key"], $this->container->parameters["fos_twitter.consumer_secret"]); // static, see 'Using multiple Codebird instances'

        $cb = new \Codebird\Codebird;
        $cb->setToken($user->getTwitterAccessToken(), $user->getTwitterAccessTokenSecret());
        $lat = $request->request->get('lat');
        $lon = $request->request->get('lon');
        $radius = $request->request->get('radius');
        if($lat!='' && is_numeric($lat) && $lon!= '' && is_numeric($lon)){
          if($radius=='' || !is_numeric($radius)){
            $radius = 10;
          }
          $geocode = "$lat,$lon,".$radius."mi";
        }else{
          $geocode = false;
        }
        $lang = $request->request->get('lang');
        $result_type = $request->request->get('result_type');
        if ($result_type == '')
            $result_type = 'mixed';
        $reply = $cb->search_tweets('count=100&q='.$request->request->get('q').(($geocode)?'&geocode='.urlencode($geocode):'').(($lang)?'&lang='.$lang:'').'&result_type='.$result_type);
        return $this->render('CosaInstantTimelineBundle:Instant:twitterSearch.html.twig', array(
            'reply' => $reply
        ));

    }

    public function twitterSearch2Action(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();
        require_once ('codebird.php');
        \Codebird\Codebird::setConsumerKey($this->container->parameters["fos_twitter.consumer_key"], $this->container->parameters["fos_twitter.consumer_secret"]); // static, see 'Using multiple Codebird instances'

        $cb = new \Codebird\Codebird;
        $cb->setToken($user->getTwitterAccessToken(), $user->getTwitterAccessTokenSecret());
        $reply = $cb->search_tweets(ltrim($request->request->get('req'), '?'));
        return $this->render('CosaInstantTimelineBundle:Instant:twitterSearch2.html.twig', array(
            'reply' => $reply
        ));

    }

    public function rmKeywordAction($keyword_id)
    {
        try{
          $keyword = $this->checkKeyword($keyword_id);
          $em = $this->getDoctrine()->getManager();
          $em->remove($keyword);
          $em->flush();
          return new JsonResponse(array('retour'=>true),200,array('Content-Type', 'application/json'));
        }catch(\Exception $e){
          return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
        }
    }

    public function addKeywordAction(Request $request, $instant_id)
    {
        $keywordToAdd = strtolower($request->request->get('keyword')); 
        $instant = $this->checkInstant2($instant_id);
        $em = $this->getDoctrine()->getManager();
        $keywordInDB = $em->getRepository('CosaInstantTimelineBundle:Keyword')->findOneBy(array('instant'=>$instant_id,'keyword'=>$keywordToAdd));

        $id = 0;

        if ($keywordInDB) {
            $id = $keywordInDB->getId();
        } else {
            $keyword = new Keyword();
            $keyword->setKeyword($keywordToAdd); 
            $keyword->setInstant($instant);

            try {
                $em->persist($keyword);
                $em->flush();
            } catch(\Exception $e) {
                return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
            }
            $id = $keyword->getId();
        }
        return new JsonResponse(array('retour'=>true,'id'=>$id),200,array('Content-Type', 'application/json'));
    }

    /**
    * check keyword
    */
    private function checkKeyword($keyword_id)
    {
        $em = $this->getDoctrine()->getManager();
        $keyword = $em->getRepository('CosaInstantTimelineBundle:Keyword')->find($keyword_id);
        if (!$keyword) {
            throw $this->createNotFoundException('This keyword does not exist');
        } else if ($keyword->getInstant()->getUser()!=$this->get('security.context')->getToken()->getuser()) {
            throw new AccessDeniedException();
        }
        return $keyword;
    }

    /**
    * check twittos
    */
    private function checkTwittos($twittos_id)
    {
        $em = $this->getDoctrine()->getManager();
        $twittos = $em->getRepository('CosaInstantTimelineBundle:Twittos')->find($twittos_id);
        if (!$twittos) {
            throw $this->createNotFoundException('This twittos does not exist');
        } else if ($twittos->getInstant()->getUser()!=$this->get('security.context')->getToken()->getuser()) {
            throw new AccessDeniedException();
        }
        return $twittos;
    }

    public function rmTwittosAction($twittos_id)
    {
        try{
          $twittos = $this->checkTwittos($twittos_id);
          $em = $this->getDoctrine()->getManager();
          $em->remove($twittos);
          $em->flush();
          return new JsonResponse(array('retour'=>true),200,array('Content-Type', 'application/json'));
        }catch(\Exception $e){
          return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
        }
    }

    public function upInstantAction(Request $request, $instant_id)
    {
        $instant = $this->checkInstant2($instant_id);
        $instant->setTitle($request->request->get('title'));
        $instant->setUrlTitle(urlencode($request->request->get('title')));
        $instant->setDescription($request->request->get('description'));
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($instant);
            $em->flush();
        } catch(\Exception $e) {
            return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
        }
        return new JsonResponse(array('retour'=>true),200,array('Content-Type', 'application/json'));
    }

    public function upTwittosAction(Request $request, $twittos_id)
    {
        $twittos = $this->checkTwittos($twittos_id);
        $twittos->setDescription($request->request->get('description'));
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($twittos);
            $em->flush();
        } catch(\Exception $e) {
            return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
        }
        return new JsonResponse(array('retour'=>true),200,array('Content-Type', 'application/json'));
    }

    public function addTwittos2Action(Request $request, $instant_id)
    {
        $instant = $this->checkInstant2($instant_id);
        $user = $this->get('security.context')->getToken()->getUser();
        $twittos_username = $request->request->get('twittos_username');
        $em = $this->getDoctrine()->getManager();
        $tuser = $em->getRepository('CosaInstantUserBundle:User')->findOneBy(array('twitter_username'=>$twittos_username));
        if(!$tuser){ // on cherche chez twitter
          try{
            require_once ('codebird.php');
            \Codebird\Codebird::setConsumerKey($this->container->parameters["fos_twitter.consumer_key"], $this->container->parameters["fos_twitter.consumer_secret"]); // static, see 'Using multiple Codebird instances'
            $cb = new \Codebird\Codebird;
            $cb->setToken($user->getTwitterAccessToken(), $user->getTwitterAccessTokenSecret());
            $reply = $cb->users_search('q='.urlencode($twittos_username));
            $ruser = false;
            $retour = array('retour'=>true,'users'=>array());
            foreach($reply as $repl){
              if(!isset($repl->screen_name)){
                break;
              }
              $retour['users'][] = array(
                'id_str'=>$repl->id_str,
                'screen_name'=>$repl->screen_name,
                'name'=>$repl->name,
                'description'=>$repl->description,
                'location'=>$repl->location,
                'profile_image_url'=>$repl->profile_image_url
              );
              if(strtoupper($repl->screen_name)==strtoupper($twittos_username)){
                $ruser = $repl;
                $last = array_pop($retour['users']);
                array_unshift($retour['users'], $last);
              }
            }
            if(count($retour['users'])>1){
              return new JsonResponse($retour,200,array('Content-Type', 'application/json'));
            }
            if($ruser!==false){
              $tuser = new User();
              $tuser->setTwitterID($ruser->id_str);
              $tuser->setTwitterUsername($ruser->screen_name);
              $tuser->setProfileImageUrl($ruser->profile_image_url);
              $tuser->setTwitterRealname($ruser->name);
              $tuser->setTwitterDescription($ruser->description);
              $tuser->setEmail($ruser->id_str);
              $tuser->setEnabled(true);
              $tuser->setPassword('');
              $tuser->setTwitterAccessToken('');
              $tuser->setTwitterAccessTokenSecret('');
              $tuser->setTwitterLocation($ruser->location);
              $tuser->setOptin(1);
              $tuser->setCreatedAt(new \DateTime('now'));
              $tuser->setUpdatedAt(new \DateTime('now'));
              $tuser->setLoginCount(0);
              $tuser->setLang('');
              $tuser->setUsername($ruser->id_str);
            }else{
              return new JsonResponse(array('retour'=>false,'msg'=>'no user found'),200,array('Content-Type', 'application/json'));
            }
          }catch(\Exception $e){
            return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
          }
        }
        if($em->getRepository('CosaInstantTimelineBundle:Twittos')->findOneBy(array('user'=>$tuser->getId(),'instant'=>$instant->getId()))){ // doublon
          return new JsonResponse(array('retour'=>false,'msg'=>'already added'),200,array('Content-Type', 'application/json'));
        }
        $twittos = new Twittos();
        $twittos->setUser($tuser);
        $twittos->setInstant($instant);
        $twittos->setAlerted(0);
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($twittos);
            $em->flush();
        } catch(\Exception $e) {
            return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
        }
        return new JsonResponse(array('retour'=>true,'twitterID'=>$tuser->getTwitterID(),'id'=>$twittos->getId(),'username'=>$tuser->getTwitterUsername(),'name'=>$tuser->getTwitterRealname(),'profile_image_url'=>$tuser->getProfileImageUrl(),'description'=>$tuser->getTwitterDescription(),'location'=>$tuser->getTwitterLocation()),200,array('Content-Type', 'application/json'));
    }

    public function addTwittosAction(Request $request, $instant_id)
    {
        $instant = $this->checkInstant2($instant_id);
        $twittos_username = $request->request->get('twittos_username');

        $em = $this->getDoctrine()->getManager();

        $twittos_user = $em->getRepository('CosaInstantUserBundle:User')->findOneBy(array('twitter_username'=>$twittos_username));

        $twittos = new Twittos();
        $twittos->setInstant($instant);
        $twittos->setAlerted(0);

        $twittosInDB = null;

        if (!$twittos_user) {
            $user = new User();
            $user->setTwitterID($request->request->get('twittos_id'));
            $user->setTwitterUsername($request->request->get('twittos_username'));
            $user->setProfileImageUrl($request->request->get('twittos_profile_image_url'));
            $user->setTwitterRealname($request->request->get('twittos_realname'));
            $user->setTwitterDescription($request->request->get('twittos_description'));
            $user->setEmail($request->request->get('twittos_id'));
            $user->setEnabled(true);
            $user->setPassword('');
            $user->setTwitterAccessToken('');
            $user->setTwitterAccessTokenSecret('');
            $user->setTwitterLocation($request->request->get('twittos_location'));
            $user->setOptin(1);
            $user->setCreatedAt(new \DateTime('now'));
            $user->setUpdatedAt(new \DateTime('now'));
            $user->setLoginCount(0);
            $user->setLang('');
            $user->setUsername($request->request->get('twittos_id'));

            $twittos->setUser($user);
        } else {
            if (
                ($twittos_user->getTwitterUsername() != $request->request->get('twittos_username'))
                || ($twittos_user->getProfileImageUrl() != $request->request->get('twittos_profile_image_url'))
                || ($twittos_user->getTwitterRealname() != $request->request->get('twittos_realname'))
                || ($twittos_user->getTwitterDescription() != $request->request->get('twittos_description'))
                || ($twittos_user->getTwitterLocation() != $request->request->get('twittos_location'))
            ) {
                $twittos_user->setTwitterUsername($request->request->get('twittos_username'));
                $twittos_user->setProfileImageUrl($request->request->get('twittos_profile_image_url'));
                $twittos_user->setTwitterRealname($request->request->get('twittos_realname'));
                $twittos_user->setTwitterDescription($request->request->get('twittos_description'));
                $twittos_user->setTwitterLocation($request->request->get('twittos_location'));
                $twittos_user->setUpdatedAt(new \DateTime('now'));
            }

            $twittos->setUser($twittos_user);
            $twittosInDB = $em->getRepository('CosaInstantTimelineBundle:Twittos')->findOneBy(array('instant'=>$instant_id, 'user'=>$twittos_user->getId()));
        }

        $id = 0;

        if ($twittosInDB) {
            //$id = $twittosInDB->getId();
            try{
              $em->persist($twittos_user);
              $em->flush();
              return new JsonResponse(array('retour'=>false,'msg'=>'already added'),200,array('Content-Type', 'application/json'));
            } catch(\Exception $e) {
                return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
            }
        } else {
            try {
                $em->persist($twittos);
                $em->flush();
            } catch(\Exception $e) {
                return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
            }
            $id = $twittos->getId();
        }
        return new JsonResponse(array('retour'=>true,'id'=>$id),200,array('Content-Type', 'application/json'));
    }

    public function addTweetAction(Request $request, $instant_id)
    {
        $instant = $this->checkInstant2($instant_id);
        // Is this tweet already attached to this instant ?
        $hasTweet = false;

        $repository = $this->getDoctrine()
                   ->getManager()
                   ->getRepository('CosaInstantTimelineBundle:Tweet');

        $tweet = $repository->findOneBy(array('twitter_id' =>$request->request->get('twitter_id')));

        if (!$tweet) {
            $tweet = new Tweet();
            $tweet->setTwitterId($request->request->get('twitter_id'));
            $tweet->setText($request->request->get('text'));
            $tweet->setName($request->request->get('name'));
            $tweet->setScreenName($request->request->get('screen_name'));
            $tweet->setUserId($request->request->get('user_id'));
            $tweet->setUserDescription($request->request->get('description'));
            $tweet->setUserLocation($request->request->get('userlocation'));
            $tweet->setProfileImageUrl($request->request->get('profile_image_url'));
            $tweet->setLocation($request->request->get('location'));
            $tweet->setMediaUrl($request->request->get('media_url'));
            $date = new \DateTime($request->request->get('created_at'));
            $tweet->setCreatedAt($date);
            $tweet->setIsRt(0);
        } else {
            if ($instant->hasTweet($tweet))
                $hasTweet = true;
        }

        if (!$hasTweet) {
            $instant->addTweet($tweet);
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($instant);
                $em->flush();
            } catch(\Exception $e) {
                return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
            }
        }
        return new JsonResponse(array('retour'=>true,'id'=>$tweet->getId()),200,array('Content-Type', 'application/json'));
    }

    public function rmTweetAction($tweet_id,$instant_id)
    {
        try{
          $tweet = $this->checkTweet($tweet_id);
          $instant = $this->checkInstant2($instant_id);
          $instant->removeTweet($tweet);
          $em = $this->getDoctrine()->getManager();
          $em->persist($instant);
          $em->flush();
          return new JsonResponse(array('retour'=>true),200,array('Content-Type', 'application/json'));
        }catch(\Exception $e){
          return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
        }
    }

    public function clearAction($instant_id)
    {
        $instant = $this->checkInstant2($instant_id);
        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository('CosaInstantTimelineBundle:Keyword')->findByInstant($instant->getId());
        foreach ($keywords as $keyword)
        {
            $em->remove($keyword);
        }
        //$keywords->clear();
        $twittos_list = $em->getRepository('CosaInstantTimelineBundle:Twittos')->findByInstant($instant->getId());
        foreach ($twittos_list as $twittos)
        {
            $em->remove($twittos);
        }
        foreach ($instant->getTweets() as $tweet)
        {
            $instant->removeTweet($tweet);
        }
        $em->flush();
        return $this->redirect($this->generateUrl('instant_edit', array('username' => $this->get('security.context')->getToken()->getUser()->getTwitterUsername(), 'instant_title' => $instant->getUrlTitle())));
    }

    public function refreshTimelineAction(Request $request, $instant_id)
    {
        try{
          $em = $this->getDoctrine()->getManager();
          $instant = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($instant_id);
          if (!$instant) {
            throw $this->createNotFoundException('Unable to find Instant entity.');
          }
          $editable = false;
          if($request->request->get('from') && $request->request->get('from')=='edit'){
            if($this->checkInstant2($instant_id)){
              $editable = true;
            }
          }
          $instantWithTweets = $em->getRepository('CosaInstantTimelineBundle:Instant')->getList($instant_id,0,100);
          $tweets = Array();
          if(count($instantWithTweets))
            $tweets = $instantWithTweets[0]->getTweets();
          $html = $this->render('CosaInstantTimelineBundle:Instant:tweetList.html.twig', array('tweets' => $tweets,'editable'=>$editable,'instant'=>$instant,'off'=>0,'nb'=>100,'icons'=>true));
          return new JsonResponse(array('retour'=>true,'html'=>$html->getContent()),200,array('Content-Type', 'application/json'));
        }catch(\Exception $e){
          return new JsonResponse(array('retour'=>false,'msg'=>$e->getMessage()),200,array('Content-Type', 'application/json'));
        }
    }

}
