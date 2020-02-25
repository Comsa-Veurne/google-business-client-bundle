<?php
namespace Cirykpopeye\GoogleBusinessClient\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GoogleBusinessClientExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->getLoader($container)->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->getLoader($container)->load('doctrine.yml');
    }

    private function getLoader(ContainerBuilder $container)
    {
        return new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    }
}
