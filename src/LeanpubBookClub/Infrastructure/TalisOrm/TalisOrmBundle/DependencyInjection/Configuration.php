<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private string $rootNodeName;

    public function __construct(string $rootNodeName)
    {
        $this->rootNodeName = $rootNodeName;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->rootNodeName);

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('aggregates')
                ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                        ->ifString()
                            ->then(static function ($v) {
                                return ['class' => $v];
                            })
                        ->end()
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
