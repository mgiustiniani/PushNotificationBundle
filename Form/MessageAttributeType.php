<?php

namespace Manticora\PushNotificationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MessageAttributeType extends AbstractType
{
    public function build(FormBuilder $builder, array $options)
    {
        $builder
            ->add('chiave')
            ->add('valore')
        ;
    }

    public function getName()
    {
        return 'manticora_pushnotificationbundle_messageattributetype';
    }
    
    public function getDefaultOptions(array $options)
    {
    	return array(
    			'data_class' => 'Manticora\PushNotificationBundle\Entity\MessageAttribute',
    
    	);
    }
}
