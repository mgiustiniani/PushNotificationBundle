<?php
namespace Manticora\PushNotificationBundle\Controller;

use Symfony\Component\DependencyInjection\Compiler\RemoveAbstractDefinitionsPass;

use Manticora\PushNotificationBundle\Entity\Client;

use Symfony\Component\Finder\Finder;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;


class PushNotificationController extends Controller {
	protected $md5;
	
	public function activeAction($token)  {
		$em = $this->getDoctrine()->getEntityManager();
		$result =	$em->getRepository("ManticoraPushNotificationBundle:Message")->findOneByActive(true);
		$response = $this->render('ManticoraPushNotificationBundle:PushNotification:active.html.twig', array('entity' => $result));
	
	
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
	public function TokenAction($type, $token)
	{
		
		$em = $this->getDoctrine()->getEntityManager();
		
		$em->getConnection()->beginTransaction();
		try { 
	
		$client = new Client();
		$client->setToken($token);
		$client->setType($type);
		$em->persist($client);
		$em->flush();
		
		$em->getConnection()->commit();
		return new Response("TIPO TELEFONO: ".$type."<br />TOKEN: ".$token);
		}
		catch (	\Exception  $e) {
			
			$em->getConnection()->rollback();
			$em->close();
		return new Response("ID_ESISTENTE");
		}
	}
	/**
	 * Invio dei token
	 * @param string $type tipo sistema mobile
	 */
	public function PushAction($type)
	{
		
		$md5='Prova Virus';
		echo '<h1>tipologia invio: '.$type.'</h1>';
   $em = $this->getDoctrine()->getEntityManager();
     
     
    
     if($type == 'all' || $type == 'blackberry' ) {
     $push = $this->get('push_notification.blackberry');
		//   $push->send($md5);
     }
      $clients = $em->getRepository('ManticoraPushNotificationBundle:Client')->findAll();
      $android_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findByType("android");
      $ios_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findByType("ios");
     $android = array();
   //  $android[] ="APA91bFirb7fzoMTLEibz325BlmZi4SRckfqgbREWXPYzgUc38f_0JGGDGFLc3VORbRWYI9A6mQ8Ofeb2SlVKIo_RcMvtaDt9l1V4HHjlOUnxfVV8fe80_EmU2IwYBwKVnRxeev2t_9XNzujTzPYTeS9Czu45DGNbg";
        foreach ($clients as $client) {
        	try { 
        		if(($type == 'all' || $type == $client->getType()) &&  strlen($client->getToken()) > 15 ) {
        			echo '<br><h2>'.$client->getType().'</H2> invio: '.  $client->getToken()."<br />";
        		//   $push = $this->get('push_notification.'.$client->getType());
        			if($type == 'android')
        			$android[] = $client->getToken();
        			$push= $this->get('push_notification.'.$type);
        			if($type == 'ios')
        		     $push->send( $md5, $client->getToken());
        		}
           	
        	} 
        	catch(Exception $e) {
        		echo 'non inviato';
        	}
        	
        }
        $push = $this->get('push_notification.android');
        $push->send( $md5, $android);
        
		
      //  $push2= $this->get('push_notification.ios');
       // $push2->feedback();

        return new Response("<br>Fine Invio");
	}
	
	protected function progress($pk, $i) {
		
		$client  =  new \Wrench\Client("ws://192.168.0.147:8001/progress","http://localhost");
		$client->connect();
		$message = array(
				"type" => "progress",
				"message"=>$pk,
				"progress"=>$i
		);
		$client->sendData(json_encode($message));
		usleep(10);
		
		$client->sendData("", 'close');
		$client->disconnect();
	}
	
	
	public function sendAction($pk)
	{
		ignore_user_abort(true);
		set_time_limit(0);
	/*	header("Content-Length: 0");
		header("Connection: close");*/
	flush();
		session_write_close();
		

		
	
		
		$em = $this->getDoctrine()->getEntityManager();
		$clients = $em->getRepository('ManticoraPushNotificationBundle:Client')->findAll();
		$message = $em->getRepository('ManticoraPushNotificationBundle:Message')->find($pk);
		
		if(!$message->getEnable()) return new Response("Message Not Enabled!!");
		if($message->getType()->getName() != 'now') return new Response("Can't send message now!");
		//return new Response("Can't send message now!");
		for ($i=0;$i<1000;$i++) {
			$this->progress($pk, $i);
		}
		
		$push_ios = $this->get('push_notification.ios');
		
		$push_android = $this->get('push_notification.android');
		$push_blackberry = $this->get('push_notification.blackberry');
		$push_ios->addMessage($message);
		$push_android ->addMessage($message);
		$push_blackberry ->addMessage($message);
		$count = 0;
		
		
		$android_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findByType("android");
		$ios_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findByType("ios");
		$blackberry_clients =$em->getRepository('ManticoraPushNotificationBundle:Client')->findByType("blackberry");
		$count = $count + count($android_clients);
		$count = $count + 2 * count($ios_clients);
		$i=0;
		foreach ($clients as $client) {
		//	$this->progress($pk, $i/$count*1000);
			$i++;
			if ($client->getType() == 'ios') $push_ios->addToken($client->getToken());
			if ($client->getType() == 'android') $push_android->addToken($client->getToken());
			if ($client->getType() == 'blackberry') $push_blackberry->addToken($client->getToken());
				
		}
				$response = $push_android->send();
		$push_ios->send();
		$push_blackberry->send();
		$response = $push_android->send();
		$removeds = $response['remove'];
		$addeds = $response['add'];
		$removeds =array_merge($removeds, $push_ios->feedback());
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
	
	
		return new Response("Fine Invio");
	}
	
	
	public function IndexAction($md5)
	{
		$finder = new Finder();
		$finder->files()->in($this->get('request')->server->get('DOCUMENT_ROOT').'/files')->name('presentazione.pdf');
		$xfile=null;

		foreach ($finder as $file) {
			
			$xfile=$file;
		$size =$this->_format_bytes($file->getSize());;
		}
		 
		$finder = new Finder();
		$finder->files()->in($this->get('request')->server->get('DOCUMENT_ROOT').'/files')->name('enel_1q_2012_results.pdf');
		
		//$finder->files()->in($this->get('request')->server->get('DOCUMENT_ROOT'))->name('app.php');
		
		$xfile2=null;
		
		foreach ($finder as $file) {
				
			$xfile2=$file;
			$size2 =$this->_format_bytes($file->getSize());
		}
		
		$finder = new Finder();
		$finder->files()->in($this->get('request')->server->get('DOCUMENT_ROOT').'/tempfiles')->name('1H2012results.pdf');
		$xfile3=null;
		
		foreach ($finder as $file) {
		
			$xfile3=$file;
			$size3 =$this->_format_bytes($file->getSize());
		}
		
		
		
		$this->getRequest()->getSession()->set('latitude', $this->getRequest()->get('latitude', 'no latitude'));
		$this->getRequest()->getSession()->set('longitude',$this->getRequest()->get('longitude', 'no longitude'));
		$latitude= $this->getRequest()->getSession()->get('latitude');
		$longitude= $this->getRequest()->getSession()->get('longitude');
		          //A7b42360df42a2fe22b4fc6d4cbb8288
		if($md5=='A7b42360df42a2fe22b4fc6d4cbb8288' && $xfile!=null)	return $this->render('ManticoraEnelInvestorRelationBundle:PushNotification:avviso.html.twig', array('file'=>$xfile, 'size'=>$size));
		if($md5=='A7s42360df42a2fe22b4fc6d4cbb8288' && $xfile2!=null)	return $this->render('ManticoraEnelInvestorRelationBundle:PushNotification:avviso2.html.twig', array('file'=>$xfile2, 'size'=>$size2));
		
		if($md5=='B5s42360df42a2fe22b4fc6d4cbb8287' && $xfile3!=null )	return $this->render('ManticoraEnelInvestorRelationBundle:PushNotification:avviso3.html.twig', array('file'=>$xfile3, 'size'=>$size3));
		
		
		
		
		if($md5=='6fb42360df42a2fe22b4fc6d4cbb8288')	return $this->render('ManticoraEnelInvestorRelationBundle:PushNotification:disclaimer.html.twig');
		else return $this->render('ManticoraPushNotificationnBundle:PushNotification:notfound.html.twig');
		
	}
	
	
	
	

	public function CheckAction()
	{
		$md5='B5s42360df42a2fe22b4fc6d4cbb8287';
		return new Response($md5);
	
	}
	
	

	
	protected function _format_bytes($a_bytes)
	{
		if ($a_bytes < 1024) {
			return $a_bytes .' B';
		} elseif ($a_bytes < 1048576) {
			return round($a_bytes / 1024, 2) .' KB';
		} elseif ($a_bytes < 1073741824) {
			return round($a_bytes / 1048576, 2) . ' MB';
		} elseif ($a_bytes < 1099511627776) {
			return round($a_bytes / 1073741824, 2) . ' GB';
		} elseif ($a_bytes < 1125899906842624) {
			return round($a_bytes / 1099511627776, 2) .' TB';
		} elseif ($a_bytes < 1152921504606846976) {
			return round($a_bytes / 1125899906842624, 2) .' PB';
		} elseif ($a_bytes < 1180591620717411303424) {
			return round($a_bytes / 1152921504606846976, 2) .' EiB';
		} elseif ($a_bytes < 1208925819614629174706176) {
			return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
		} else {
			return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
		}
	}
	
	private function _generateRandomString($length = 10, $addSpaces = true, $addNumbers = true)
	{
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"§$%&/()=[]{}';
		$useChars = array();
		// select some random chars:
		for($i = 0; $i < $length; $i++)
		{
		$useChars[] = $characters[mt_rand(0, strlen($characters)-1)];
		}
		// add spaces and numbers:
		if($addSpaces === true)
		{
			array_push($useChars, ' ', ' ', ' ', ' ', ' ', ' ');
		}
		if($addNumbers === true)
		{
			array_push($useChars, rand(0,9), rand(0,9), rand(0,9));
		}
		shuffle($useChars);
		$randomString = trim(implode('', $useChars));
		$randomString = substr($randomString, 0, $length);
		return $randomString;
	}

}
