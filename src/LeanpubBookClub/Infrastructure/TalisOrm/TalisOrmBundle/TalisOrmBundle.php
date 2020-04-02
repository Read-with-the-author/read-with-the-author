<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle;

use LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle\DependencyInjection\Compiler\ReplaceSchemaProviderPass;
use LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle\DependencyInjection\Compiler\SetAggregateClassesArgument;
use LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle\DependencyInjection\TalisOrmExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class TalisOrmBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ReplaceSchemaProviderPass());
        $container->addCompilerPass(new SetAggregateClassesArgument());
    }

    public function getContainerExtension()
    {
        return new TalisOrmExtension();
    }
}
