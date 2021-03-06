<?php

namespace Manticora\PushNotificationBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
	public function deactivateAll()
	{
		return $this->getEntityManager()->createQuery("UPDATE ManticoraPushNotificationBundle:Message m SET m.active = false")->execute();
				
	}
}