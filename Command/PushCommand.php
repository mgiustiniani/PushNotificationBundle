<?php
namespace Manticora\PushNotificationBundle\Command;

use Manticora\PushNotificationBundle\driver\PushManager;

use Manticora\PushNotificationBundle\Entity\Message;
use Manticora\PushNotificationBundle\Entity\MessageAttribute;
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
class PushCommand extends ContainerAwareCommand {
	protected $em;
	protected $input;
	protected $output;
	protected $message;
	protected $pk;

	protected function configure() {
		$this->setName('push:send')->setDescription('Invio Push')
				->addArgument('id', InputArgument::REQUIRED,
						'Inserire l\'id del messaggio');

	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->input = $input;
		$this->output = $output;

		$pk = $input->getArgument('id');
		$this->pk = $pk;
		$this->output->writeln("<info>tipologia invio: </info>");
		$this->em = $this->getContainer()->get('doctrine')->getEntityManager();
		$this->message = $this->em
				->getRepository('ManticoraPushNotificationBundle:Message')
				->find($pk);

      /*  $this->message = new Message();
        $this->message->setEnable(true);
        $messageattrib = new MessageAttribute();
        $messageattrib->setChiave('title');
        $messageattrib->setValore('messaggio inviato');
        $this->message->addMessageAttribute($messageattrib);*/
		if ($this->isValid())
			$this->send();

	}

	public function isValid() {

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

	public function send() {

		$em = $this->getContainer()->get('doctrine')->getEntityManager();

		for ($i = 0; $i < 50; $i++) {
			$this->progress($this->pk,($i+1)*10);
		}
		
		
		
		$push_manager = new PushManager($this->getContainer()->get('doctrine'), $this->getContainer());
		$push_manager->setOutput($this->output);
		$push_manager->send($this->pk);
	for ($i = 50; $i < 100; $i++) {
			$this->progress($this->pk,($i+1)*10);
		}
		die();
		/**
		 * Android Push Send
		 */
		if (count($android_clients) > 0)	
		{		$response = $push_android->send();
		$removeds = $response['remove'];
		$addeds = $response['add'];

		$repos = $em->getRepository('ManticoraPushNotificationBundle:Client');
	/*	foreach ($removeds as $removed) {
			try {

				$this->output->writeln("<info>Delete'.$removed.</info>");
				$token = $repos->findOneByToken(trim($removed));

				$em->remove($token);
				$em->flush($token);
			} catch (\InvalidArgumentException $e) {
				$this->output
						->writeln(
								"<error>Invalid: " . $e->getMessage()
										. "</error>");
			}
		}*/
		}

		$push_android->clearToken();

		/**
		 * Blackberry Push Send
		 */
		if (count($blackberry_clients) > 0)			$push_blackberry->send();
		/**
		 *  IOS PUSH SEND
		 */
		if (count($ios_clients) > 0)
			$push_ios->send();

		$removeds = $push_ios->feedback();
		foreach ($removeds as $removed) {
			$this->output->writeln("<info>Delete: '.$removed.</info>");
			$token = $em
					->getRepository('ManticoraPushNotificationBundle:Client')
					->findOneByToken($removed);
			$em->remove($token);
			$em->flush($token);
		}


	}

	protected function progress($pk, $i) {
		$client = $this->getContainer()
				->get('push_notification_websocketclient');//  new \Wrench\Client("ws://192.168.0.147:8000/progress","http://generali");
		$client->connect();
		$message = array("type" => "progress", "message" => $pk, "progress" => $i);
		$client->sendData(json_encode($message));
		usleep(6000);

		$client->sendData("", 'close');
		$client->disconnect();
	}
}
