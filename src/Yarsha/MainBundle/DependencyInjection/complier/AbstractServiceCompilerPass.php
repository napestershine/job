<?php
/**
 * Created by PhpStorm.
 * User: zone
 * Date: 1/20/17
 * Time: 10:03 AM
 */

namespace Yarsha\MainBundle\DependencyInjection\complier;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AbstractServiceCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            "yarsha.abstract_service"
        );

        foreach ($taggedServices as $id => $tags) {
            $registry = $container->findDefinition($id);
            $registry->addMethodCall(
                'setEntityManager',
                [
                    new Reference("doctrine.orm.entity_manager")
                ]
            );
            $registry->addMethodCall(
                'setPaginationService',
                [
                    new Reference("yarsha.service.pagination")
                ]
            );
            $registry->addMethodCall(
                'setTokenStorage',
                [
                    new Reference("security.token_storage")
                ]
            );
        }

    }
}
