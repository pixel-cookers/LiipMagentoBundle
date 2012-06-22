<?php

namespace Liip\MagentoBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LiipMagentoExtension extends Extension
{
    private $resources = array(
        'services.xml'
    );

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('liip_magento.mage_file', $config['mage_file']);
        $container->setParameter('liip_magento.session_namespace', $config['session_namespace']);
        $container->setParameter('liip_magento.store_mappings', $config['store_mappings']);
        $container->setParameter('liip_magento.session_parameters', $config['session_parameters']);

        $this->loadDefaults($container);

        foreach ($config['service'] as $key => $service) {
            $container->setAlias($this->getAlias().'.'.$key, $config['service'][$key]);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected function loadDefaults($container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ($this->resources as $resource) {
            $loader->load($resource);
        }
    }
}
