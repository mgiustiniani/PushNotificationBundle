<?php
/**
 * Created by PhpStorm.
 * User: mgiustiniani
 * Date: 03/12/13
 * Time: 11.36
 */

namespace Manticora\PushNotificationBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Manticora\PushNotificationBundle\Entity\MessageType;

class LoadPushTypeData implements FixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
         $type = new MessageType();
         $type->setName('cron');
         $type->setDescription('Messaggio Schedulato da CRON');
         $manager->persist($type);

        $type = new MessageType();
        $type->setName('activable');
        $type->setDescription('Messaggio pubblicato in HTTP');
        $manager->persist($type);

        $type = new MessageType();
        $type->setName('now');
        $type->setDescription('Messaggio da inviare con pulsante');
        $manager->persist($type);

        $manager->flush();
    }


} 