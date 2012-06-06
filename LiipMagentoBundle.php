<?php

namespace Liip\MagentoBundle;

use Liip\MagentoBundle\DependencyInjection\Compiler\MagentoCompilerPass;
use Liip\MagentoBundle\Security\MagentoAuthenticationFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LiipMagentoBundle extends Bundle
{
   public function build(ContainerBuilder $container)
   {
       parent::build($container);
       $container->addCompilerPass(new MagentoCompilerPass());

       $extension = $container->getExtension('security');
       $extension->addSecurityListenerFactory(new MagentoAuthenticationFactory());

   }
}