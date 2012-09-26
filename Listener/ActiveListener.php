<?php
namespace Manticora\PushNotificationBundle\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;

use Doctrine\ORM\EntityManager;

use Manticora\PushNotificationBundle\Entity\Message;

use Doctrine\ORM\Event\LifecycleEventArgs;
class ActiveListener {
	protected $entity;
	protected $em;
	protected $args;
	public function PrePersist(LifecycleEventArgs $args) {
		
		$this->args = $args;
	//	$this->deactive();
		
	}
	public function PreUpdate(PreUpdateEventArgs $args) {
		$this->args = $args;
	if(!	$args->hasChangedField('active')) return; 
		$this->deactive();
		
	}

	protected function deactive() {
		$entity = $this->args->getEntity();
		$em = $this->args->getEntityManager();
	 
		if ($this->args->getEntity() instanceof Message) {
			if(!$entity->getEnable() || $entity->getType()->getName() != 'activable') {
				if($this->args  instanceof PreUpdateEventArgs)
				$this->args->setNewValue('active', false);
				return;
			}

			if($entity->getActive()){
		
				$em = $this->args->getEntityManager()->getRepository("ManticoraPushNotificationBundle:Message")->deactivateAll();
		
			}
		}
	}

}
