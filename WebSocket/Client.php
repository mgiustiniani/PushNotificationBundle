<?php
namespace Manticora\PushNotificationBundle\WebSocket;


use Wrench\Client as WrenchClient;
class Client extends WrenchClient{
		public function __construct($host, $path) {
			parent::__construct($host.'/'.$path, 'http://localhost');
		}
}
