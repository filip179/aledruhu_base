<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $root = $treeBuilder
            ->root('app', 'array')
            ->children();

        $this->addBasicSection($root);
        $this->addSidebarSection($root);
        $this->addMetaSection($root);

        return $treeBuilder;
    }

    private function addBasicSection(NodeBuilder $builder)
    {
        $builder
            ->scalarNode('login_logo')
            ->isRequired()
            ->end()
            ->scalarNode('admin_logo')
            ->isRequired()
            ->end();
    }

    private function addSidebarSection(NodeBuilder $builder)
    {
        $builder
            ->arrayNode('sidebar')
            ->requiresAtLeastOneElement()
            ->prototype('array')
            ->children()
            ->scalarNode('title')
            ->isRequired()
            ->end()
            ->scalarNode('icon')
            ->isRequired()
            ->end()
            ->scalarNode('route')
            ->isRequired()
            ->end()
            ->end()
            ->end()
            ->end();
    }

    private function addMetaSection(NodeBuilder $builder)
    {
        $builder
            ->arrayNode('meta')
            ->children()
            ->scalarNode('title')
            ->isRequired()
            ->end()
            ->scalarNode('description')
            ->isRequired()
            ->end()
            ->end()
            ->end();
    }

}
