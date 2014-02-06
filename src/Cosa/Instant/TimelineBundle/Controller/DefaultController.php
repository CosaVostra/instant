<?php

namespace Cosa\Instant\TimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CosaInstantTimelineBundle:Default:index.html.twig', array('name' => $name));
    }
}
