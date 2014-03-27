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
            return $this->redirect($this->generateUrl('instant_list',array('username'=>$this->get('security.context')->getToken()->getUser()->getTwitterUsername())));
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

    public function settingsAction(Request $request)
    {
      $user = $this->get('security.context')->getToken()->getUser();
      $old_email = $user->getEmail();
      $formBuilder = $this->createFormBuilder($user);
      $formBuilder->add('email', 'email');
      $formBuilder->add('optin', 'checkbox', array('required'=>false));
      $form = $formBuilder->getForm();
      
      if ($request->getMethod() == 'POST') {
        $form->bind($request);
        if ($form->isValid()){
          $em = $this->getDoctrine()->getManager();
          if($user->getEmail()!=$old_email){
            $translator = $this->get('translator');
            $user->setConfirmationToken(hash('sha256',$user->getUsername().$user->getEmail()));
            $to = $user->getEmail();
            $subject = $translator->trans('Confirmation email subject');
            $message = $translator->trans('Confirmation email message', array('%twitterRealName%' => $user->getTwitterRealname(), '%validationUrl%' => $this->generateUrl('email_validation',array('token'=>$user->getConfirmationToken()),true)));
            $headers = "From: no-reply@createinstant.com\r\nX-Mailer: PHP/" . phpversion();
            mail($to, $subject, $message, $headers);
            $this->get('session')->getFlashBag()->add('notice', $translator->trans('Email updated message'));
          }
          $em->persist($user);
          $em->flush();
        }
      }

      return $this->render('CosaInstantUserBundle:Default:settings.html.twig', array(
        'form' => $form->createView(),
      ));
    }

    public function setEmailTmpAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if($user->getEmail()==$user->getTwitterID())
          $user->setEmail('');
        // On crée le FormBuilder grâce à la méthode du contrôleur
        $formBuilder = $this->createFormBuilder($user);
        $formBuilder->add('email', 'email');
        $form = $formBuilder->getForm();

        if ($request->getMethod() == 'POST') {
          $form->bind($request);

          if ($form->isValid() && $user->getEmail()!=''){
            try{
              $translator = $this->get('translator');
              $em = $this->getDoctrine()->getManager();
              $user->setConfirmationToken(hash('sha256',$user->getUsername().$user->getEmail()));
              $em->persist($user);
              $em->flush();
              $to = $user->getEmail();
              $subject = $translator->trans('Confirmation email subject');
              $message = $translator->trans('Confirmation email message', array('%twitterRealName%' => $user->getTwitterRealname(), '%validationUrl%' => $this->generateUrl('email_validation',array('token'=>$user->getConfirmationToken()),true)));
              $headers = "From: no-reply@createinstant.com\r\nX-Mailer: PHP/" . phpversion();

              mail($to, $subject, $message, $headers);
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

    public function emailValidationAction($token)
    {
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CosaInstantUserBundle:User')->findOneByConfirmationToken($token);
        if(!$user){
          throw $this->createNotFoundException('This token does not exist');
        }
        $user->setConfirmationToken('confirmed');
        $em->persist($user);
        $em->flush();
        //$this->get('session')->getFlashBag()->add('notice', $translator->trans('Email confirmed message'));
        return $this->redirect($this->generateUrl('homepage'));
    }
}
