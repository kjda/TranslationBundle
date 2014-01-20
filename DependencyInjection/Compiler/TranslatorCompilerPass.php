<?php

namespace Kjda\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
class TranslatorCompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {

        if (!$container->hasDefinition('kjda_translator')) {
          return;
        }

        $kjdaTranslator = $container->findDefinition('kjda_translator');
        $default = $container->findDefinition('translator.default');
        $default->setClass('Kjda\TranslationBundle\Translation\Translator');
        $default->setMethodCalls(array_merge($default->getMethodCalls(), $kjdaTranslator->getMethodCalls()));
        $default->addMethodCall('setLocale', array(0 => $container->getParameter('kernel.default_locale')));
    }

}
