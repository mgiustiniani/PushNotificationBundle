<?php

namespace Manticora\PushNotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('PushNotificationBundle:Default:index.html.twig', array('name' => $name));
    }
}
