<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class TalisOrmExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration($this->getAlias());

        $config = $this->processConfiguration($configuration, $configs);

        $aggregateClasses = array_map(
            function (array $aggregate): string {
                return $aggregate['class'];
            },
            $config['aggregates']
        );

        $container->setParameter('talis_orm.aggregate_classes', $aggregateClasses);
    }

    public function getAlias(): string
    {
        return 'talis_orm';
    }
}
