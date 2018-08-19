<?php

/*
 * This file is part of the RollerworksMultiUserBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yarsha\EmployerBundle;

use Rollerworks\Bundle\MultiUserBundle\DependencyInjection\Compiler\RegisterFosUserMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class YarshaEmployerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(RegisterFosUserMappingsPass::createOrmMappingDriver('yarsha_employer'));
    }
}
