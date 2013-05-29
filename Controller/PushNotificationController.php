<?php
namespace Manticora\PushNotificationBundle\Controller;
use Manticora\PushNotificationBundle\driver\PushManager;

use Symfony\Component\DependencyInjection\Compiler\RemoveAbstractDefinitionsPass;

use Manticora\PushNotificationBundle\Entity\Client;

use Symfony\Component\Finder\Finder;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

class PushNotificationController extends Controller {
	protected $md5;

	public function activeAction($token) {
		$em = $this->getDoctrine()->getEntityManager();
		$result = $em->getRepository("ManticoraPushNotificationBundle:Message")
				->findOneByActive(true);
		$response = $this
				->render(
						'ManticoraPushNotificationBundle:PushNotification:active.html.twig',
						array('entity' => $result));

		return $response;

	}
	/**
	 * inserimento Token
	 *
	 * Cool Class Diagram
	[Message|+cron (string);+enable (boolean);+push_all (boolean);+start_time;+stop_time]
	[Attribute|+key;+value]
	[MessageType|+name;+description]
	[Client|+token;+type;+description]
	[Message]1-0..*[Attribute]
	[Message]1-0..*[Client]
	[MessageType]1-0..*[Message]
	 *
	 */
	public function TokenAction($type, $token) {

		$em = $this->getDoctrine()->getEntityManager();

		$em->getConnection()->beginTransaction();
		try {

			$client = new Client();
			$client->setToken($token);
			$client->setType($type);
			$em->persist($client);
			$em->flush();

			$em->getConnection()->commit();
			return new Response("TIPO TELEFONO: " . $type . "<br />TOKEN: "
					. $token);
		} catch (\Exception $e) {

			$em->getConnection()->rollback();
			$em->close();
			return new Response("ID_ESISTENTE");
		}
	}

	protected function progress($pk, $i) {

		$client = $this->get('push_notification_websocketclient');
		$client->connect();
		$message = array("type" => "progress", "message" => $pk, "progress" => $i);
		$client->sendData(json_encode($message));
		usleep(1);

		$client->sendData("", 'close');
		$client->disconnect();
	}

	public function sendAction($pk) {
		ignore_user_abort(true);
		set_time_limit(0);
		header("Content-Length: 0");
		header("Connection: close");
		flush();
		session_write_close();

		

	/*	if (!$message->getEnable())
			return new Response("Message Not Enabled!!");
		if ($message->getType()->getName() != 'now')
			return new Response("Can't send message now!");*/
		//return new Response("Can't send message now!");
		for ($i = 0; $i < 50; $i++) {
			$this->progress($pk,($i+1)*10);
		}

		
		$push_manager = new PushManager($this->getDoctrine(), $this->container);
		//$push_manager->setOutput($this->output);
		$push_manager->send($pk);

		for ($i = 50; $i < 100; $i++) {
			$this->progress($pk,($i+1)*10);
		}
		
		return new Response("Fine Invio");
	}

}
