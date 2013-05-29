<?php
namespace Manticora\PushNotificationBundle\driver;

use Manticora\PushNotificationBundle\Entity\Message;

class AndroidPushNotification implements abstractPushNotification {

	protected $api_key;
	protected $message;
	protected $tokens = array();
	protected $progress_id;

	public function __construct($api_key) {
		$this->api_key = $api_key;
		$this->message = new \Zend_Mobile_Push_Message_Gcm();
		$this->progress_id = 0;
	}

	public function addToken($token) {
		//$this->message->addToken($token);

		$this->tokens[$this->progress_id][] = $token;
		if ((count($this->tokens[$this->progress_id]) % 300) == 0)
			$this->progress_id++;
		

	}
	public function addData($key, $value) {
		$this->message->addData($key, $value);

	}

	public function clearToken() {
		$this->message->clearToken();

	}
	public function send() {

		$removed = array();
		$added = array();
		$gcm = new \Zend_Mobile_Push_Gcm();
		$gcm->setApiKey($this->api_key);
		foreach ($this->tokens as $key => $tokens) {
			$this->message->setToken($tokens);
		try {
			

				
				$response = $gcm->send($this->message);
				
				
		} catch (\Zend_Mobile_Push_Exception $e) {
			// exceptions require action or implementation of exponential backoff.
			die($e->getMessage());
		}

		// handle all errors and registration_id's
//		foreach ($responses as $response) {
			foreach ($response->getResults() as $k => $v) {
		//		var_dump($response->getResults());
				if (isset($v['registration_id'])) {
					printf("Response: %s has a new registration id of: %s\r\n", $k, $v['registration_id']);
					$removed[] = $k;
					$added[] = $v['registration_id'];
				}
				if (isset($v['error'])) {
					printf("Response: %s had an error of: %s\r\n", $k, $v['error']);
					// InvalidRegistration quando id è completamente sbagliato
					// NotRegistered Applicazione disinstallata
					if ($v['error'] == 'InvalidRegistration')
						$removed[] = $k;
					if ($v['error'] == 'NotRegistered')
						$removed[] = $k;
					if ($v['error'] == 'MismatchSenderId')
						$removed[] = $k;
				}
				if (isset($v['message_id'])) {
			//		printf("Response: %s was successfully sent the message, message id is: %s", $k, $v['message_id']);
				}

	//		}
		}
		
		}
		$this->message->clearToken();
		$result['add'] = $added;
		$result['remove'] = $removed;
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
			$this->message->addData($key, $value->getValore());
		}

	}

}
