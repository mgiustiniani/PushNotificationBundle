<?php
namespace Manticora\PushNotificationBundle\driver;
use Manticora\PushNotificationBundle\Entity\Message;
require_once("Zend/Loader.php");
interface  abstractPushNotification {


	public function addToken($token);
	public function addData($key, $value);
	public function send();
	public function addMessage(Message $message);

}
