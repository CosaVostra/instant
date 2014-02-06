<?php

namespace Cosa\Instant\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function loginAction()
    {//var_dump($this->get('session'));
var_dump($this->get('security.context')->getToken());//->getUser());
        return $this->render('CosaInstantUserBundle:Default:login.html.twig');
    }

    public function logoutAction(Request $request)
    {var_dump($this->get('session'));exit;
        $request->getSession()->clear();
        return $this->redirect($this->generateUrl('homepage'));
    }

    public function loginCheckAction(Request $request)
    {
        return $this->redirect($this->generateUrl('homepage'));
    }

    public function connectTwitterAction()
    {
        if($this->get('security.context')->isGranted('ROLE_USER'))
            return $this->redirect($this->generateUrl('homepage'));
        $request = $this->get('request');
        $twitter = $this->get('fos_twitter.service');
        $authURL = $twitter->getLoginUrl($request);
        $response = new RedirectResponse($authURL);
        return $response;
    }
}
