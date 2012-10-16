<?php
namespace Manticora\PushNotificationBundle\Controller;
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
		usleep(10);

		$client->sendData("", 'close');
		$client->disconnect();
	}

	public function sendAction($pk) {
		ignore_user_abort(true);
		set_time_limit(0);
		/*	header("Content-Length: 0");
		    header("Connection: close");*/
		flush();
		session_write_close();

		$em = $this->getDoctrine()->getEntityManager();
		$clients = $em->getRepository('ManticoraPushNotificationBundle:Client')
				->findAll();
		$message = $em
				->getRepository('ManticoraPushNotificationBundle:Message')
				->find($pk);

		if (!$message->getEnable())
			return new Response("Message Not Enabled!!");
		if ($message->getType()->getName() != 'now')
			return new Response("Can't send message now!");
		//return new Response("Can't send message now!");
		for ($i = 0; $i < 1000; $i++) {
			$this->progress($pk, $i);
		}

		$push_ios = $this->get('push_notification.ios');

		$push_android = $this->get('push_notification.android');
		$push_blackberry = $this->get('push_notification.blackberry');
		$push_ios->addMessage($message);
		$push_android->addMessage($message);
		$push_blackberry->addMessage($message);
		$count = 0;

		$android_clients = $em
				->getRepository('ManticoraPushNotificationBundle:Client')
				->findByType("android");
		$ios_clients = $em
				->getRepository('ManticoraPushNotificationBundle:Client')
				->findByType("ios");
		$blackberry_clients = $em
				->getRepository('ManticoraPushNotificationBundle:Client')
				->findByType("blackberry");
		$count = $count + count($android_clients);
		$count = $count + 2 * count($ios_clients);
		$i = 0;
		foreach ($clients as $client) {
			//	$this->progress($pk, $i/$count*1000);
			$i++;
			if ($client->getType() == 'ios')
				$push_ios->addToken($client->getToken());
			if ($client->getType() == 'android')
				$push_android->addToken($client->getToken());
			if ($client->getType() == 'blackberry')
				$push_blackberry->addToken($client->getToken());

		}
		$response = $push_android->send();

		if (count($ios_clients) > 0)
			$push_ios->send();
		if (count($blackberry_clients) > 0)
			$push_blackberry->send();
		if (count($android_clients) > 0)
			$response = $push_android->send();
		$removeds = $response['remove'];
		$addeds = $response['add'];
		$removeds = array_merge($removeds, $push_ios->feedback());
		foreach ($addeds as $add) {
			$token = new \Manticora\PushNotificationBundle\Entity\Client();

			$token2 = $em
					->getRepository('ManticoraPushNotificationBundle:Client')
					->findOneByToken($add);
			if (is_null($token2)) {
				$token->setType('android');
				$token->setToken($add);
				$em->persist($token);
				$em->flush($token);
			}
		}
		foreach ($removeds as $removed) {
			$token = $em
					->getRepository('ManticoraPushNotificationBundle:Client')
					->findOneByToken($removed);
			$em->remove($token);
			$em->flush($token);
		}

		return new Response("Fine Invio");
	}

}
