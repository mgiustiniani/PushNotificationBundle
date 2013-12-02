<?php

namespace Manticora\PushNotificationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MessageTypeType extends AbstractType
{
    public function build(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
        ;
    }

    public function getName()
    {
        return 'manticora_pushnotificationbundle_messagetypetype';
    }
}
