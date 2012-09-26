<?php

namespace Manticora\PushNotificationBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ManticoraPushNotificationExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
    	
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
      
        if (!isset($config['ios']['cert'])) {
        	throw new \InvalidArgumentException('The iOS APNS "certificate" ios is not set');
        }
        
        if (!isset($config['android']['api_key'])) {
        	throw new \InvalidArgumentException('The Android GCM "api_key" ios is not set');
        }
        if ($config['blackberry']['env'] == 'prod' && !isset($config['blackberry']['cpid'])) {
        	throw new \InvalidArgumentException('The Blackberry Production "Content Provider ID" is not set');
        }
        $container->setParameter('push_notification.blackberry.app_id', $config['blackberry']['app_id']);
        $container->setParameter('push_notification.blackberry.password', $config['blackberry']['password']);
        $container->setParameter('push_notification.blackberry.env', $config['blackberry']['env']);
        $container->setParameter('push_notification.blackberry.cpid', $config['blackberry']['cpid']);
        $container->setParameter('push_notification.ios.cert', $config['ios']['cert']);
        $container->setParameter('push_notification.ios.cert', $config['ios']['cert']);
        $container->setParameter('push_notification.ios.env', $config['ios']['env']);
        $container->setParameter('push_notification.ios.passphrase', $config['ios']['passphrase']);
        $container->setParameter('push_notification.android.api_key', $config['android']['api_key']);
        
     
    }
}
