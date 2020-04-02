<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle\DependencyInjection\Compiler;

use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle\DoctrineMigrations\AggregateMigrationsSchemaProvider;
use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ReplaceSchemaProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $this->getDiffCommandDefinition($container);

        /**
         * @see DiffCommand::__construct()
         */
        $definition->setArguments(
            [
                new Reference(AggregateMigrationsSchemaProvider::class)
            ]
        );

        $container->setDefinition(DiffCommand::class, $definition);
    }

    private function getDiffCommandDefinition(ContainerBuilder $container): Definition
    {
        if ($container->hasDefinition('doctrine_migrations.diff_command')) {
            return $container->getDefinition('doctrine_migrations.diff_command');
        }

        throw new RuntimeException('Could not find the service definition of the diff command');
    }
}
