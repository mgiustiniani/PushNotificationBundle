<?php
namespace Manticora\PushNotificationBundle\driver;
use Manticora\PushNotificationBundle\Entity\Message;
class BlackBerryPushNotification implements abstractPushNotification {

	protected $pap;
	protected $alert;
	protected $token = array();

	public function __construct($appid, $password, $env, $cpid = null) {

		$this->pap = new \Zend_Mobile_Push_Pap($appid, $password);
		$this->pap->setEnvironment($env);
		if($env == 'prod')
		 $this->pap->setContentProviderId($cpid);

	}
	public function send() {

		$message = new \Zend_Mobile_Push_Message_Pap($this->alert, null, '+5 seconds');
		
		foreach ($this->token as $token)  {
			
			$message->addTo($token);
		}

		$response = $this->pap->send($message);
		if ($response->isError()) {
			echo '<br><b> BlackBerry Push::Descrizione errore:</b> '
					. $response->getErrorString();

			echo '<br><b> BlackBerry Push::Descrizione errore:</b> '
					. $response->getErrorString();
		} else
			echo '<br /><b> BlackBerry Push::Descrizione risposta:</b> '
					. $response->getResponseDesc();

	}
	public function addToken($token) {
		$this->token[] = $token;

	}
	public function addData($key, $value) {
		// TODO: Auto-generated method stub

	}
	public function addMessage(Message $message) {
		$values = $message->getAttributes()->toArray();
		if(isset($values['blackberry']))
		$this->alert = $values['blackberry'];

	}

}
