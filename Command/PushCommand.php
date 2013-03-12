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
class PushCommand extends ContainerAwareCommand {
	protected $em;
	protected $input;
	protected $output;
	protected $message;

	protected function configure() {
		$this->setName('generali:send')->setDescription('Invio Push')
				->addArgument('id', InputArgument::REQUIRED,
						'Inserire l\'id del messaggio');

	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->input = $input;
		$this->output = $output;

		$pk = $input->getArgument('id');
		$this->output->writeln("<info>tipologia invio: </info>");
		$this->em = $this->getContainer()->get('doctrine')->getEntityManager();
		$this->message = $this->em
				->getRepository('ManticoraPushNotificationBundle:Message')
				->find($pk);

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

		$clients = $em->getRepository('ManticoraPushNotificationBundle:Client')
				->findAll();
		$push_ios = $this->getContainer()->get('push_notification.ios');

		$push_android = $this->getContainer()->get('push_notification.android');

		$push_blackberry = $this->getContainer()
				->get('push_notification.blackberry');

		$push_blackberry->addMessage($this->message);
		$push_ios->addMessage($this->message);
		$push_android->addMessage($this->message);

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

		echo "Count " . $count . PHP_EOL;
		$count = $count + count($android_clients);
		echo "Count " . $count . PHP_EOL;
		$count = $count + 2 * count($ios_clients);
		echo "Count " . $count . PHP_EOL;
		$i = 0;
		foreach ($clients as $client) {
			$i++;
			if ($client->getType() == 'ios')
				$push_ios->addToken($client->getToken());
			if ($client->getType() == 'android')
				$push_android->addToken($client->getToken());

			if ($client->getType() == 'blackberry')
				$push_blackberry->addToken($client->getToken());

		}
		$dialog = $this->getHelperSet()->get('dialog');
		if (!$dialog
				->askConfirmation($this->output,
						'<question>Continue with this action?</question>',
						false)) {
			return;
		}

		/**
		 * Android Push Send
		 */
		if (count($android_clients) < -1)	
		{		$response = $push_android->send();
		$removeds = $response['remove'];
		$addeds = $response['add'];

		$repos = $em->getRepository('ManticoraPushNotificationBundle:Client');
		foreach ($removeds as $removed) {
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
		}
		}

		$push_android->clearToken();

		/**
		 * Blackberry Push Send
		 */
		if (count($blackberry_clients) < -1)			$push_blackberry->send();
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

		/*	$removeds = $response['remove'];
		    $addeds = $response['add'];
		    foreach ($addeds as $add) {
		        
		        
		        $token2  =$em->getRepository('ManticoraPushNotificationBundle:Client')->findOneByToken($add);
		        if (is_null($token2)) {
		        $token  = new \Manticora\PushNotificationBundle\Entity\Client();
		        $token->setType('android');
		        $token->setToken($add);
		        $em->persist($token);
		        $em->flush($token);
		        }
		    }
		    foreach ($removeds as $removed) {
		        $token  =$em->getRepository('ManticoraPushNotificationBundle:Client')->findOneByToken($removed);
		        $em->remove($token);
		        $em->flush($token);
		    }
		    $push_ios->send();*/

	}

	protected function progress($pk, $i) {
		$client = $this->getContainer()
				->get('push_notification_websocketclient');//  new \Wrench\Client("ws://192.168.0.147:8000/progress","http://generali");
		$client->connect();
		$message = array("type" => "progress", "message" => $pk, "progress" => $i);
		$client->sendData(json_encode($message));
		usleep(1000);

		$client->sendData("", 'close');
		$client->disconnect();
	}
}
