<?php

namespace Cosa\Instant\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function loginAction()
    {
        return $this->render('CosaInstantUserBundle:Default:login.html.twig');
    }

    public function logoutAction(Request $request)
    {
        $request->getSession()->clear();
        return $this->redirect($this->generateUrl('homepage'));
    }

    public function loginCheckAction(Request $request)
    {
        //$request->get('
        return $this->redirect($this->generateUrl('homepage'));
//        return $this->render('CosaInstantUserBundle:Default:login.html.twig');
    }

    public function connectTwitterAction()
    {   
        $request = $this->get('request');
        $twitter = $this->get('fos_twitter.service');
        $authURL = $twitter->getLoginUrl($request);
        $response = new RedirectResponse($authURL);
        return $response;
    }
}
