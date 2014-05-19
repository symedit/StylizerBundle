<?php

/*
 * This file is part of the SymEdit package.
 *
 * (c) Craig Blanchette <craig.blanchette@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymEdit\Bundle\StylizerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SymEditStylizerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $bundles = $container->getParameter('kernel.bundles');
        $yamlFiles = $this->getYamlStyleFiles($bundles);

        $container->setParameter('symedit_stylizer.loader.files.yaml', $yamlFiles);

        $loader->load('services.xml');

        $env = $container->getParameter('kernel.environment');

        /**
         * This plugs into the AsseticController when in dev mode
         */
        if (strtolower($env) !== 'prod') {
            $loader->load('services_dev.xml');
        }
    }

    private function getYamlStyleFiles($bundles)
    {
        $files = array();
        foreach ($bundles as $bundle) {
            $class = new \ReflectionClass($bundle);
            $dir = dirname($class->getFileName());
            $file = $dir.'/Resources/config/styles.yml';
            if (file_exists($file)) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'symedit_stylizer';
    }
}
