<?php

namespace Liip\MagentoBundle\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MagentoAuthenticationFactory extends FormLoginFactory
{
    public function __construct()
    {
        $this->addOption('login_type', 'customer');
        parent::__construct();
    }

    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        $node->children()->scalarNode('login_type')->end()->end();
    }

    public function getKey()
    {
        return 'magento';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = 'security.authentication.provider.magento.' . $id;
        $container->setDefinition($provider, new DefinitionDecorator('security.authentication.provider.magento'))
                ->replaceArgument(0, new Reference($userProviderId))
                ->replaceArgument(2, $id)
                ->replaceArgument(3, isset($config['login_type']) ? $config['login_type'] : 'customer');

        if ($container->hasDefinition('security.logout_listener.' . $id)) {
            $container->getDefinition('security.logout_listener.' . $id)
                    ->addMethodCall('addHandler', array(new Reference($provider)));
        }

        return $provider;
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        $container->getDefinition($listenerId)->replaceArgument(10, new Reference('security.user.provider.magento.' . $config['login_type']));

        return $listenerId;
    }

    protected function getListenerId()
    {
        return 'security.authentication.listener.magento';
    }
}
