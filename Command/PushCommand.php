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
class PushCommand extends ContainerAwareCommand{
	protected $em;
	protected $input;
	protected $output;
	protected $message;
	
	protected function configure() {
		$this->setName('generali:send')
				->setDescription('Invio Push')
				->addArgument('id', InputArgument::REQUIRED,
						'Inserire l\'id del messaggio');

	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->input = $input;
		$this->output = $output;
		
	
	/*
	$client->disconnect();
		return;
	
		$process = new Process("crontab -l");
		$process->run();
	$data = explode("\n", $process->getOutput());
	
		foreach ($data as $key => $line )
		{
			
	
		
	
	}
	$this->em = $this->getContainer()->get('doctrine')->getEntityManager();
	$messages = $this->em->getRepository('ManticoraPushNotificationBundle:Message')->findAll();
	foreach ($messages as $message) {
		
		$data[] = trim($message->getCronstring()).' /var/www/generali/app/console generali:send '.$message->getId();
		
		
	}
	
	
		}
		return;*/
		
		
		
		
		
		$pk = $input->getArgument('id');
		$this->output->writeln("<info>tipologia invio: </info>");
			$this->em = $this->getContainer()->get('doctrine')->getEntityManager();
		$this->message = $this->em->getRepository('ManticoraPushNotificationBundle:Message')->find($pk);
		
	if($this->isValid()) $this->send();
		
	
	
		

	}
	
	
	public function isValid() {
		
		
		if(!$this->message->getEnable()) return false ;
		if($this->message->getStartTime() && $this->message->getStartTime() > New \DateTime() ) return false;

		if($this->message->getStopTime() && $this->message->getStopTime() < New \DateTime() ) return false;
	
		return true;
		
		
		
		
	}
	
	
	public function send() {
		
		
		$em = $this->getContainer()->get('doctrine')->getEntityManager();
		
		
		$clients = $em->getRepository('ManticoraPushNotificationBundle:Client')->findAll();
		$push_ios = $this->getContainer()->get('push_notification.ios');
		
		$push_android = $this->getContainer()->get('push_notification.android');
		$push_ios->addMessage($this->message);
		$push_android ->addMessage($this->message);
		
		$count = 0;
		
		
		$android_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findByType("android");
		$ios_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findByType("ios");
		
		echo "Count ".$count.PHP_EOL;
		$count = $count + count($android_clients);
		echo "Count ".$count.PHP_EOL;
		$count = $count + 2 * count($ios_clients);
		echo "Count ".$count.PHP_EOL;
		$i=0;
		foreach ($clients as $client) {
			//	$this->progress($pk, $i/$count*1000);
			$i++;
			if ($client->getType() == 'ios') $push_ios->addToken($client->getToken());
			if ($client->getType() == 'android') $push_android->addToken($client->getToken());
		
		
		}
		for($i=0 ; $i<1000;$i++) {
			$this->progress($this->message->getId(), $i+1);
		}
	//	$push_ios->send();
		$response = $push_android->send();
		$removeds = $response['remove'];
		$addeds = $response['add'];
		foreach ($addeds as $add) {
			$token  = new \Manticora\PushNotificationBundle\Entity\Client();
			$token->setType('android');
			$token->setToken($add);
			$em->persist($token);
			$em->flush($token);
		}
		foreach ($removeds as $removed) {
			$token  =$em->getRepository('ManticoraPushNotificationBundle:Client')->findOneByToken($removed);
			$em->remove($token);
			$em->flush($token);
		}
		$push_ios->send();
		
	}
	
	protected function progress($pk, $i) {
		$client  =  new \Wrench\Client("ws://192.168.0.147:8000/progress","http://generali");
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
}
