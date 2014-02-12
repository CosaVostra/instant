<?php

namespace Cosa\Instant\TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CosaInstantTimelineBundle:Default:index.html.twig', array('name' => $name));
    }

    public function pleaseConfirmEmailAction()
    {
        return $this->render('CosaInstantTimelineBundle:Default:please_confirm_email.html.twig');
    }

    public function homepageAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return $this->redirect($this->generateUrl('instant_list', array('username' => $user->getUsername())));
    }

    public function aboutAction()
    {
        return $this->render('CosaInstantTimelineBundle:Default:about.html.twig');
    }

    public function whoWeAreAction()
    {
        return $this->render('CosaInstantTimelineBundle:Default:who_we_are.html.twig');
    }
}
