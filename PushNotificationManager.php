<?php
namespace Manticora\PushNotificationBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Manticora\PushNotificationBundle\driver\IosPushNotification;

class PushNotificationManager {
	
	
	
	//protected $container;
	
	/*public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	*/
	public function send($type){
		echo 'ciao';
		print_r($this);
		$push = $this->get('push_notification.ios.man');
		//$push -> send('ciao');
	}

}
