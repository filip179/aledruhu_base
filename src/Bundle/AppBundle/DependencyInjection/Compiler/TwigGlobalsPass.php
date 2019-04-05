<?php

namespace AppBundle\DependencyInjection\Compiler;

use AppBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigGlobalsPass implements CompilerPassInterface {
    public function process(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig('app');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('twig');
        $definition->addMethodCall('addGlobal', ['login_logo', $config['login_logo']]);
        $definition->addMethodCall('addGlobal', ['admin_logo', $config['admin_logo']]);
        $definition->addMethodCall('addGlobal', ['meta_title', $config['meta']['title']]);
        $definition->addMethodCall('addGlobal', ['meta_description', $config['meta']['description']]);
    }

    private function processConfiguration(ConfigurationInterface $configuration, array $configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $configs);
    }
}
