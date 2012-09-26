<?php

namespace Manticora\PushNotificationBundle\Listener;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use Doctrine\ORM\Mapping\PrePersist;


use Doctrine\ORM\Event\LifecycleEventArgs;
class messageSaveListener {
	
	public function PrePersist(LifecycleEventArgs $args) {
		
	}
	public function PreUpdate(PreUpdateEventArgs $args) {
	}

}
