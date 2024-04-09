<?php

namespace SWouters\SqlMigrationsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SqlMigrationsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loaderConfig = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );
        $loaderConfig->load('services.yaml');
    }
}
