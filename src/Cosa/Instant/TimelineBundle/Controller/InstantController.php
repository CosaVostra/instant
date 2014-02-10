<?php

namespace Cosa\Instant\TimelineBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Cosa\Instant\TimelineBundle\Entity\Instant;
use Cosa\Instant\TimelineBundle\Form\InstantType;

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

            return $this->redirect($this->generateUrl('instant_edit', array('username' => $username, 'instant_title' => $entity->getTitle())));
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
        $user = $this->get('security.context')->getToken()->getUser();
        $entity = new Instant();
        $entity->setUser($user);
        $form   = $this->createForm(new InstantType(), $entity);

        return $this->render('CosaInstantTimelineBundle:Instant:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'user'   => $user,
        ));
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

    /**
     * Displays a form to edit an existing Instant entity.
     *
     */
    public function editAction($username,$instant_title)
    {
        $user = $this->checkUser($username);
        $entity = $this->checkInstant($user,$instant_title);
        $editForm = $this->createForm(new InstantType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);
        return $this->render('CosaInstantTimelineBundle:Instant:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        //    'delete_form' => $deleteForm->createView(),
            'user'        => $user,
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
    public function updateAction(Request $request, $username, $instant_title)
    {
        if($request->request->get('bsubmit')=='publish'){
            $editForm = $this->save($request, $username, $instant_title, true);
        }else{
            $editForm = $this->save($request, $username, $instant_title);
        }
        if ($editForm === true) {
            return $this->redirect($this->generateUrl('instant_edit', array('username' => $username, 'instant_title' => $instant_title)));
        }
        return $this->render('CosaInstantTimelineBundle:Instant:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
            'user'        => $user,
        ));
    }

    /**
     * Deletes a Instant entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Instant entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('instant'));
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
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Instant entity.');
        }

        // On crée le FormBuilder grâce à la méthode du contrôleur
        $formBuilder = $this->createFormBuilder($entity);
        $formBuilder->add('messageType', 'text');
        $form = $formBuilder->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
          $form->bind($request);

          if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $twittos = $em->getRepository('CosaInstantTimelineBundle:Twittos')->findByInstant($id);
            foreach ($twittos as $twitto) {
              // ATTENTION : Une requête SQL à chaque itération !!
              $user = $twitto->getUser();
            }

            return $this->redirect($this->generateUrl('about'));
          }
        }

        return $this->render('CosaInstantTimelineBundle:Instant:alertTwittos.html.twig', array(
            'form' => $form->createView(),
        ));
        
   /**
    * check user
    */
    private function checkUser($username)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CosaInstantUserBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException('This user does not exist');
        } else if ($user != $this->get('security.context')->getToken()->getUser()) {
            throw new AccessDeniedException();
        }
        return $user;
    }

   /**
    * check instant
    */
    private function checkInstant($user,$title)
    {
        $em = $this->getDoctrine()->getManager();
        $instant = $em->getRepository('CosaInstantTimelineBundle:Instant')->findOneBy(array('user'=>$user->getId(),'title'=>$title));
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
    public function userInstantListAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->checkUser($username);
        $entities = $em->getRepository('CosaInstantTimelineBundle:Instant')->findByUser($user->getId());
        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
        }
        return $this->render('CosaInstantTimelineBundle:Instant:userInstantList.html.twig', array(
            'entities' => $entities,
            'deleteForms' => $deleteForms,
            'user' => $user,
        ));
    }

    /**
    * Show Instant webview code from a given user
    *
    * @param string $username The user name
    * @param string $instant_title The instant title
    */
    public function userInstantWebviewAction($username,$instant_title)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CosaInstantUserBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException('This user does not exist');
        }
        $instant = $em->getRepository('CosaInstantTimelineBundle:Instant')->findOneBy(array('user'=>$user->getId(),'title'=>$instant_title));
        if (!$instant) {
            throw $this->createNotFoundException('This instant does not exist');
        }
        return $this->render('CosaInstantTimelineBundle:Instant:userInstantWebview.html.twig', array(
            'instant' => $instant
        ));
    }

    /**
    * Show Instant preview code from a given user
    *
    * @param string $username The user name
    * @param string $instant_title The instant title
    */
    public function userInstantPreviewAction($username,$instant_title)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->checkUser($username);
        $instant = $this->checkInstant($user,$instant_title);
        return $this->render('CosaInstantTimelineBundle:Instant:userInstantPreview.html.twig', array(
            'instant' => $instant,
            'user' => $user
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
        $em = $this->getDoctrine()->getManager();
        $user = $this->checkUser($username);
        $instant = $this->checkInstant($user,$instant_title);
        return $this->render('CosaInstantTimelineBundle:Instant:userInstantEmbed.html.twig', array(
            'instant' => $instant,
            'user' => $user
        ));
    }

}
