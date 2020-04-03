<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TalisOrm\Schema\AggregateSchemaProvider;

final class SetAggregateClassesArgument implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(AggregateSchemaProvider::class);
        $definition->setArguments(
            [
                $container->getParameter('talis_orm.aggregate_classes')
            ]
        );
    }
}
