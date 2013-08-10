<?php
/*
 * This file is part of the Eko\GoogleTranslateBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\GoogleTranslateBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EkoGoogleTranslateExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('methods.xml');

        $container->setParameter('eko_google_translate.api_key', $config['api_key']);

        $this->loadProfilerCollector($container, $loader);
    }

    /**
     * Loads profiler collector for correct environments
     *
     * @param ContainerBuilder $container Symfony dependency injection container
     * @param XmlFileLoader    $loader    XML file loader
     */
    protected function loadProfilerCollector(ContainerBuilder $container, XmlFileLoader $loader)
    {
        if ($container->hasParameter('kernel.debug')) {
            $loader->load('collector.xml');

            foreach ($container->findTaggedServiceIds('eko.google_translate.method') as $id => $attributes) {
                $serviceDefinition = $container->getDefinition($id);
                $serviceDefinition->addArgument(new Reference('debug.stopwatch'));

                $container->setDefinition($id, $serviceDefinition);
            }
        }
    }
}
