<?php

namespace Cosa\Instant\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function loginAction()
    {//var_dump($this->get('session'));
//var_dump($this->get('security.context')->getToken());//->getUser());
        if($this->get('security.context')->isGranted('ROLE_USER')){
            return $this->redirect($this->generateUrl('instant_list',array('username'=>$this->get('security.context')->getToken()->getUser()->getUsername())));
        }
        return $this->render('CosaInstantUserBundle:Default:login.html.twig');
    }

    public function logoutAction(Request $request)
    {var_dump($this->get('session'));exit;
        $request->getSession()->clear();
        return $this->redirect($this->generateUrl('login'));
    }

    public function loginCheckAction(Request $request)
    {
        return $this->redirect($this->generateUrl('login'));
    }

    public function connectTwitterAction()
    {
        if($this->get('security.context')->isGranted('ROLE_USER'))
            return $this->redirect($this->generateUrl('login'));
        $request = $this->get('request');
        $twitter = $this->get('fos_twitter.service');
        $authURL = $twitter->getLoginUrl($request);
        $response = new RedirectResponse($authURL);
        return $response;
    }

/*    public function setEmailSmallAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        // On crée le FormBuilder grâce à la méthode du contrôleur
        $formBuilder = $this->get('form.factory')->createNamedBuilder('form_email_small', $user);
        $formBuilder->add('email', 'email');
        $form = $formBuilder->getForm();
        return $this->render('CosaInstantUserBundle:Default:setEmailSmall.html.twig', array(
            'form' => $form->createView(),
        ));
    }*/

    public function setEmailTmpAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        // On crée le FormBuilder grâce à la méthode du contrôleur
        $formBuilder = $this->createFormBuilder($user);
        $formBuilder->add('email', 'email');
        $form = $formBuilder->getForm();

        if ($request->getMethod() == 'POST') {
          $form->bind($request);

          if ($form->isValid()) {
            try{
              $em = $this->getDoctrine()->getManager();
              $em->persist($user);
              $em->flush();
              return new JsonResponse(array('retour'=>true),200,array('Content-Type', 'application/json'));
            }catch(\Exception $e){
              return new JsonResponse(array('retour'=>false),200,array('Content-Type', 'application/json'));
            }
          }else{
            return new JsonResponse(array('retour'=>false),200,array('Content-Type', 'application/json'));
          }
        }

        return $this->render('CosaInstantUserBundle:Default:setEmail.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
}
