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


    public function createPushMenu($menu){
        $this->addLinkRoute($menu,'Messaggi', 'Manticora_PushNotificationBundle_Message_list');
        $this->addLinkRoute($menu,'Template Messaggi',  'Manticora_PushNotificationBundle_MessageTemplate_list');
        $this->addLinkRoute($menu,'Tipo Messaggi',  'Manticora_PushNotificationBundle_MessageType_list');
        $this->addLinkRoute($menu,'Clienti',  'Manticora_PushNotificationBundle_Client_list');
        $this->addLinkRoute($menu,'Gruppi',  'Manticora_PushNotificationBundle_MessageGroup_list');
        $this->addLinkRoute($menu,'Attributi',  'Manticora_PushNotificationBundle_MessageAttribute_list');
    }

}
