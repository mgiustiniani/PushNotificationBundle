<?php
namespace Manticora\PushNotificationBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAware;
class Builder   extends ContainerAware{
	private $factory;
	
	/**
	 * @param \Knp\Menu\FactoryInterface $factory
	 */
	public function __construct(FactoryInterface $factory)
	{
		$this->factory = $factory;
	}
	
	/**
	 * @param Request $request
	 * @param Router $router
	 */
	public function createAdminMenu(Request $request)
	{
		$menu = $this->factory->createItem('Push Manager', array('route' => 'Manticora_PushNotificationBundle_Message_list') );
	
		$menu->addChild('Messaggi', array('route' => 'Manticora_PushNotificationBundle_Message_list'));
		$menu->addChild('Template Messaggi', array('route' => 'Manticora_PushNotificationBundle_MessageTemplate_list'));
		$menu->addChild('Tipo Messaggi', array('route' => 'Manticora_PushNotificationBundle_MessageType_list'));
		$menu->addChild('Clienti', array('route' => 'Manticora_PushNotificationBundle_Client_list'));
		$menu->addChild('Gruppi', array('route' => 'Manticora_PushNotificationBundle_MessageGroup_list'));
		$menu->addChild('Attributi', array('route' => 'Manticora_PushNotificationBundle_MessageAttribute_list'));
		return $menu;
		
		
		
	
		
	
	}

}
