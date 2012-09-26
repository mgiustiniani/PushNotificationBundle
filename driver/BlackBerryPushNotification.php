<?php
namespace Manticora\PushNotificationBundle\driver;
class BlackBerryPushNotification implements abstractPushNotification {

	protected $pap;

	public function __construct($appid, $password) {

		$this->pap = new \BlackBerryPap($appid, $password);

	}
	public function send($alert, $device_token = 'push_all') {

		$message = new \BlackBerryMessage($alert, null, '+5 seconds');
		$message->addTo($device_token);

		$response = $this->pap->push($message);
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
		// TODO: Auto-generated method stub

	}
	public function addData($key, $value) {
		// TODO: Auto-generated method stub

	}
	public function addMessage(Message $message) {
		// TODO: Auto-generated method stub

	}

}
