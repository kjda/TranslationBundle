<?php

namespace Kjda\TranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Kjda\TranslationBundle\DependencyInjection\Compiler\TranslatorCompilerPass;

class KjdaTranslationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TranslatorCompilerPass());
    }
}