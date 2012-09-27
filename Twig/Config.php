<?php
namespace Manticora\PushNotificationBundle\Twig;
class Config extends \Twig_Extension {
	protected $url;
	protected $app;
	
	public function __construct($url, $app) {
		$this->url = $url;
		$this->app = $app;
	}
	
	public function getName(){
		return 'push';
	}
	
	public function getGlobals()
	{
		return array(
				'push_ws_url' => $this->url,
				'push_ws_application' => $this->app,
	
		);
	}

}
