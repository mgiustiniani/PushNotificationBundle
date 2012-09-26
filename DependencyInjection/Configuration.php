<?php

namespace Manticora\PushNotificationBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface {
	/**
	 * {@inheritDoc}
	 */
	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('manticora_push_notification');
		$this->addIosSection($rootNode);
		$this->addAndroidSection($rootNode);
		$this->addBlackBerrySection($rootNode);
		return $treeBuilder;
	}

	private function addIosSection(ArrayNodeDefinition $rootNode) {
		$rootNode->
			children()->
				arrayNode('ios')->
					children()
						->scalarNode('cert')->end()
						->scalarNode('passphrase')->defaultNull()->end()
						->scalarNode('env')->defaultValue('dev')->end()
						->scalarNode('sound')->defaultValue('0')->end()
						->scalarNode('badge')->defaultValue('0')->end()
					->end()
				->end()
			->end();
	}
	
	private function addAndroidSection(ArrayNodeDefinition $rootNode) {
		$rootNode->
			children()->
				arrayNode('android')->
					children()
						->scalarNode('api_key')->end()
					->end()
				->end()
		->end();
	}
	private function addBlackBerrySection(ArrayNodeDefinition $rootNode) {
		$rootNode->
			children()->
				arrayNode('blackberry')->addDefaultsIfNotSet()->
					children()
						->scalarNode('app_id')->defaultNull()->end()
						->scalarNode('password')->defaultNull()->end()
						->scalarNode('env')->defaultValue('dev')->end()
						->scalarNode('cpid')->defaultNull()->end()
					->end()
				->end()
			->end();
	}
}
