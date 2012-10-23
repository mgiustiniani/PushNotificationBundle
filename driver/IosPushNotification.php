<?php
namespace Manticora\PushNotificationBundle\driver;
use Manticora\PushNotificationBundle\Entity\Message;

class IosPushNotification implements abstractPushNotification {

	protected $cert;
	protected $passphrase;
	protected $env;
	protected $token = array();
	protected $data;
	protected $message;

	protected $alert;
	public function __construct($cert, $passphrase, $env = 'prod') {
		$this->cert = $cert;
		$this->passphrase = $passphrase;
		echo $passphrase;
		if ($env == 'prod') {
			$this->env['send'] = \Zend_Mobile_Push_Apns::SERVER_PRODUCTION_URI;
			$this->env['feedback'] = \Zend_Mobile_Push_Apns::SERVER_FEEDBACK_PRODUCTION_URI;
		} else {
			$this->env['send'] = \Zend_Mobile_Push_Apns::SERVER_SANDBOX_URI;
			$this->env['feedback'] = \Zend_Mobile_Push_Apns::SERVER_FEEDBACK_SANDBOX_URI;
		}
		$this->message = new \Zend_Mobile_Push_Message_Apns();
		$this->alert = array('body' => null, 'action-loc-key' => null,
				'loc-key' => null, 'loc-args' => null,
				'launch-image' => null,);

	}
	public function addToken($token) {
		$this->token[] = $token;
	}
	public function addData($key, $value) {

		if ($key == 'body') {
			$this->message->setAlert($value);
			echo $value;
		} else
			$this->message->addCustomData($key, $value);

	}
	public function send() {
		$removed = array();
		foreach ($this->token as $token) {
			try {

				$apns = new \Zend_Mobile_Push_Apns();
				$apns->setCertificate($this->cert); // REPLACE WITH YOUR CERT
				if(is_string($this->passphrase))
					$apns->setCertificatePassphrase($this->passphrase);
				$this->message->setToken($token); // REPLACE WITH A APNS TOKEN
				$this->message->setId(time());
				$this->message->setBadge(1);
				$apns->connect($this->env['send']);
				$apns->send($this->message);
				$apns->close();

			}catch (\Zend_Mobile_Push_Exception_InvalidToken $e) {
				$removed[] =$this->message->getToken();
				echo '<br>Caught exception: ', $e->getMessage(), "\n";
			} catch (\Exception $e) {
				echo '<br>Caught exception: ', $e->getMessage(), "\n";
			}
		}
		return $removed;
	}

	public function feedback() {
$removed = array();
		try {
			$apns = new \Zend_Mobile_Push_Apns();
			$apns->setCertificate($this->cert); // REPLACE WITH YOUR CERT
			if(is_string($this->passphrase))
					$apns->setCertificatePassphrase($this->passphrase);
			$apns->connect($this->env['feedback']);
			$feedbacks = $apns->feedback();
			print_r($feedbacks);

			$apns->close();
			echo '<br>tokens<br>';

			foreach ($feedbacks as $token => $feed) {
				$date = new \DateTime('@' . $feed);
				
				$removed[] = $token;
				echo 'token: ' . $token . '<br>data di disinstallazione: '
						. $date->format('Y-m-d H:i:sP') . "<br>";

			}
			
		} catch (\Exception $e) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
		return $removed;
	}
	public function addMessage(Message $message) {
		$values = $message->getAttributes()->toArray();
		/** dovrebbe fornire le chiavi dell'alert */
		$alert_news = array_intersect_key($values, $this->alert);
		$this->alert = array_merge($this->alert, $alert_news);
		$customdata = array_diff_key($values, $this->alert);
		$this->message
				->setAlert((string) $this->alert['body'],
						isset($this->alert['action-loc-key']) ? $this->alert['action-loc-key'] : null,
						(string) $this->alert['loc-key'],
						json_decode($this->alert['loc-args']),
						(string) $this->alert['launch-image']);
		
		/** dovrebbe fornire le chiavi rimanenti */
		foreach ($customdata as $key => $value) {
			$this->message->addCustomData($key, $value);
		}

	}

}
