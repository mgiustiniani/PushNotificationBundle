<?php
namespace Manticora\PushNotificationBundle\Menu;

use Symfony\Component\HttpFoundation\Request;

use Admingenerator\GeneratorBundle\Menu\AdmingeneratorMenuBuilder;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Routing\Router;
class Builder   extends AdmingeneratorMenuBuilder{

    public function navbarMenu(FactoryInterface $factory, array $options) {
        // create root item
        $menu = $factory->createItem('root');
        // set id for root item, and class for nice twitter bootstrap style
        $menu->setChildrenAttributes(array('id' => 'main_navigation', 'class' => 'nav'));


        $push=$this->addDropdown($menu, 'Push Notification',true);

        $this->createPushMenu($push);
        return $menu;
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

    public function createPushMenu($menu){
        $this->addLinkRoute($menu,'Messaggi', 'Manticora_PushNotificationBundle_Message_list');
    }

}
