<?php

namespace Dontdrinkandroot\UtilsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ddr_utils');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
