<?php
namespace Manticora\PushNotificationBundle\driver;
use Manticora\PushNotificationBundle\Entity\Message;

class AndroidPushNotification implements abstractPushNotification {

	protected $api_key;
	protected $message;

	public function __construct($api_key) {
		$this->api_key = $api_key;
		$this->message = new \Zend_Mobile_Push_Message_Gcm();

	}

	public function addToken($token) {
		$this->message->addToken($token);

	}
	public function addData($key, $value) {
		$this->message->addData($key, $value);

	}
	public function send() {
echo "API KEY".$this->api_key;

		$removed = array();
		$added = array();
		$gcm = new \Zend_Mobile_Push_Gcm();
		$gcm->setApiKey($this->api_key);
		try {
			$response = $gcm->send($this->message);
		} catch (\Zend_Mobile_Push_Exception $e) {
			// exceptions require action or implementation of exponential backoff.
			die($e->getMessage());
		}

		// handle all errors and registration_id's
		foreach ($response->getResults() as $k => $v) {
			if (isset($v['registration_id'])) {
				printf(
						"<br>Response: %s has a new registration id of: %s\r\n",
						$k, $v['registration_id']);
				$removed[]=$k;
				$added[]=$v['registration_id'];
			}
			if (isset($v['error'])) {
				printf("<br>Response: %s had an error of: %s\r\n", $k,
						$v['error']);
				// InvalidRegistration quando id è completamente sbagliato
				// NotRegistered Applicazione disinstallata
				if($v['error'] =='InvalidRegistration') $removed[]=$k;
				if($v['error'] =='NotRegistered') $removed[]=$k;
			}
			if (isset($v['message_id'])) {
				printf(
						"<br>Response: %s was successfully sent the message, message id is: %s",
						$k, $v['message_id']);
			}
		
		}
		$result['add']=$added;
		$result['remove']=$removed;
		return $result;
	}
	protected function getBackOffTime($fails, \Zend_Http_Response $response) {
		if ($retry = $response->getHeader('Retry-After')) {
			if (is_string($retry)) {
				$retry = strtotime($retry) - time();
			}
			return (int) $retry;
		}
		return intval(pow(2, $fails) - 1);
	}
	public function addMessage(Message $message) {
		$values = $message->getAttributes()->toArray();
		foreach ($values as $key => $value) {
			$this->message->addData($key,(string) $value);
		}

	}

}
