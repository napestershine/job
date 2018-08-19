<?php

namespace Yarsha\MainBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Yarsha\MainBundle\DependencyInjection\complier\AbstractServiceCompilerPass;

class YarshaMainBundle extends Bundle
{

    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);
        $builder->addCompilerPass(new AbstractServiceCompilerPass());
    }
}
