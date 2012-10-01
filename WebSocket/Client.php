<?php
namespace Manticora\PushNotificationBundle\WebSocket;


use Wrench\Client;
class Client extends Client{
		public function __construct($host, $path) {
			parent::__construct($host.$path, 'http://localhost');
		}
}
