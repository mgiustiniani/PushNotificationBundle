<?php

namespace Manticora\PushNotificationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MessageType extends AbstractType
{
    public function build(FormBuilder $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('cronstring')
            ->add('enable')
            ->add('push_all')
            ->add('start_time')
            ->add('stop_time')
            ->add('group')
            ->add('type')
            ->add('clients')
        ;
    }

    public function getName()
    {
        return 'manticora_pushnotificationbundle_messagetype';
    }
}
