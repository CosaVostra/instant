<?php

namespace Cosa\Instant\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

    public function setEmailTmpAction(Request $request)
    {
        // On crée le FormBuilder grâce à la méthode du contrôleur
        $formBuilder = $this->createFormBuilder($this->get('security.context')->getToken()->getUser());
        $formBuilder->add('email', 'email');
        $form = $formBuilder->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
          $form->bind($request);

          if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($this->get('security.context')->getToken()->getUser());
            $em->flush();

            return $this->redirect($this->generateUrl('about'));
          }
        }

        return $this->render('CosaInstantUserBundle:Default:setEmail.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
}
