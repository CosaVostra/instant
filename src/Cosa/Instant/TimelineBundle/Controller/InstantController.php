<?php

namespace Cosa\Instant\TimelineBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function indexAction()
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
    }

    /**
     * Creates a new Instant entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Instant();
        $form = $this->createForm(new InstantType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('instant_show', array('id' => $entity->getId())));
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
        $entity = new Instant();
        $form   = $this->createForm(new InstantType(), $entity);

        return $this->render('CosaInstantTimelineBundle:Instant:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Instant entity.
     *
     */
    public function showAction($id)
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
    }

    /**
     * Displays a form to edit an existing Instant entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Instant entity.');
        }

        $editForm = $this->createForm(new InstantType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CosaInstantTimelineBundle:Instant:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Instant entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CosaInstantTimelineBundle:Instant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Instant entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InstantType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('instant_edit', array('id' => $id)));
        }

        return $this->render('CosaInstantTimelineBundle:Instant:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
        
    }

}
