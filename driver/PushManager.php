<?php
namespace Manticora\PushNotificationBundle\driver;

use Symfony\Bundle\DoctrineBundle\Registry;

class PushManager {
	
	protected $em;
	protected $id;
	protected $message;
	protected $clients;
	protected $output;
	protected $container;
	
	public function __construct( Registry $doctrine, $container) {
		$this->em = $doctrine->getEntityManager();
	//	$this->output = output;
		
		$this->container = $container;
	}
	protected function getContainer() {
		return $this->container;
	}
	
	public function setOutput($output) {
		$this->output = $output;
	}
	public function send($id) {
		$this->_id = $id;
		$this->setMessage($id);
		if(!$this->isValid()) return;
		$i = 0;
		
		$push_ios = $this->getContainer()->get('push_notification.ios');
		
		$push_android = $this->getContainer()->get('push_notification.android');
		
		$push_blackberry = $this->getContainer()
		->get('push_notification.blackberry');
		
		$push_blackberry->addMessage($this->message);
		$push_ios->addMessage($this->message);
		$push_android->addMessage($this->message);
		
		$count = 0;
		
		$android_clients = $this->em
		->getRepository('ManticoraPushNotificationBundle:Client')
		->findByType("android");
		$ios_clients = $this->em
		->getRepository('ManticoraPushNotificationBundle:Client')
		->findByType("ios");
		$blackberry_clients = $this->em
		->getRepository('ManticoraPushNotificationBundle:Client')
		->findByType("blackberry");

		foreach ($this->clients as $client) {
			$i++;
			if ($client->getType() == 'ios')
				$push_ios->addToken($client->getToken());
			if ($client->getType() == 'android')
				$push_android->addToken($client->getToken());
		
			if ($client->getType() == 'blackberry')
				$push_blackberry->addToken($client->getToken());
		
		}
		
		
		
		/**
		 * Android Push Send
		 */
		if (count($android_clients) > 0)
		{		$response = $push_android->send();
		$removeds = $response['remove'];
		$addeds = $response['add'];
		
		$repos = $this->em->getRepository('ManticoraPushNotificationBundle:Client');
		echo PHP_EOL.'count: '.count($removeds).PHP_EOL;
		foreach ($removeds as $removed) {
			try {
		
				$this->output->writeln("<info>Delete: ".$removed."</info>");

				$token = $repos->findOneByToken(trim($removed));
				$this->output->writeln("<info>Find: ".$token."</info>");
				$this->em->remove($token);

				$this->em->flush($token);
			} catch (\InvalidArgumentException $e) {
				$this->output
				->writeln(
						"<error>Invalid: " . $e->getMessage()
						. "</error>");
			}
		}
		}
		$push_android->clearToken();
		
		/**
		 * Blackberry Push Send
		*/
		/**
		 *  IOS PUSH SEND
		*/
		if (count($ios_clients) > 0) $removeds=	$push_ios->send();
		var_dump($removeds);
		$removeds = array_merge($removeds, $push_ios->feedback());
		foreach ($removeds as $removed) {
			$this->output->writeln("<info>Delete: ".$removed."</info>");

				$token = $repos->findOneByToken(trim($removed));
				$this->output->writeln("<info>Find: ".$token."</info>");
				$this->em->remove($token);

				$this->em->flush($token);
		}

		if (count($blackberry_clients) > 0)			$push_blackberry->send();
		
		
	}
	public function setMessage($id) {
		$this->message = $this->em
		->getRepository('ManticoraPushNotificationBundle:Message')
		->find($id);
		$this->setClients();
		
		
		
		
		
	}
	
	public function setClients() {
		$this->clients = $this->em->
		getRepository('ManticoraPushNotificationBundle:Client')
				->findAll();
	}
	
	protected function isValid() {
	
		if (!$this->message->getEnable())
			return false;
		if ($this->message->getStartTime()
				&& $this->message->getStartTime() > New \DateTime())
			return false;
	
		if ($this->message->getStopTime()
				&& $this->message->getStopTime() < New \DateTime())
			return false;
	
		return true;
	
	}

}
