<?php
namespace Manticora\PushNotificationBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;

use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\Process\Process;

use Wrench\Client;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelector;
class PushTestCommand extends ContainerAwareCommand {
	protected $em;
	protected $input;
	protected $output;
	protected $message;
	protected function configure() {
		$this->setName('push:test')
				->setDescription('Invio Push');

	}

	protected function progress($pk, $i) {
		$client  =  new \Wrench\Client("ws://127.0.0.1:8000/progress","http://generali");
		$client->connect();
		$message = array(
				"type" => "progress",
				"message"=>$pk,
				"progress"=>$i
		);
		$client->sendData(json_encode($message));
		usleep(1000);
	
		$client->sendData("", 'close');
		$client->disconnect();
	}
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->input = $input;
		$this->output = $output;
		$pk=1;
	$em = $this->getContainer()->get('doctrine')->getEntityManager();
		$clients = $em->getRepository('ManticoraPushNotificationBundle:Client')->findAll();
		$message = $em->getRepository('ManticoraPushNotificationBundle:Message')->find($pk);
		
		if(!$message->getEnable()) return $this->output->writeln("<info>tipologia invio: </info>");
		if($message->getType()->getName() != 'now') $this->output->writeln("<info>tipologia invio: </info>");
		//return new Response("Can't send message now!");
		
		
		$push_ios = $this->getContainer()->get('push_notification.ios');
		
		$push_android = $this->getContainer()->get('push_notification.android');
		$push_ios->addMessage($message);
		$push_android ->addMessage($message);
		
		$count = 0;
		
		
		$android_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findByType("android");
		$ios_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findByType("ios");
		
		echo "Count ".$count.PHP_EOL;
		$count = $count + count($android_clients);
		echo "Count ".$count.PHP_EOL;
		$count = $count + 2 * count($ios_clients);
		echo "Count ".$count.PHP_EOL;
		$i=0;
		for($i=0 ; $i<1000;$i++) {
			$this->progress($pk, $i+1);
		}
		foreach ($clients as $client) {
		//	$this->progress($pk, $i/$count*1000);
			$i++;
			if ($client->getType() == 'ios') $push_ios->addToken($client->getToken());
			if ($client->getType() == 'android') $push_android->addToken($client->getToken());
				
				
		}
		
		$push_ios->send();
		$removeds = $push_android->send();
		foreach ($removeds as $removed) {
			$token = $android_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findOneByToken($removed);
		$em->remove($token);
		$em->flush($token);
		}
		//$push_ios->send();
		 
	}
	
	
	
	
	
	
}
