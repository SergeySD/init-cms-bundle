<?php

/**
 * This file is part of the Networking package.
 *
 * (c) net working AG <info@networking.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Networking\InitCmsBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;


/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 * @author net working AG <info@networking.ch>
 */
class NetworkingInitCmsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();
        $defaults = Yaml::parse(__DIR__ . '/../Resources/config/config.yml');

        foreach ($configs as $config) {
            foreach ($config as $key => $value) {
                $defaults['networking_init_cms'][$key] = $value;

            }
        }

        $config = $this->processConfiguration($configuration, $defaults);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $config['languages'] = $this->addShortLabels($config['languages']);

        $container->setParameter('networking_init_cms.page.languages', $config['languages']);
        $container->setParameter('networking_init_cms.page.templates', $config['templates']);
        $container->setParameter('networking_init_cms.page.content_types', $config['content_types']);
        $container->setParameter('networking_init_cms.init_cms_editor', $config['init_cms_editor']);
        $container->setParameter(
            'networking_init_cms.translation_fallback_route',
            $config['translation_fallback_route']
        );
        $container->setParameter('networking_init_cms.404_template', $config['404_template']);
        $container->setParameter('networking_init_cms.no_translation_template', $config['no_translation_template']);
        $container->setParameter('networking_init_cms.ckeditor_config', $config['ckeditor_config']);
        $container->setParameter('networking_init_cms.admin_menu_groups', $config['admin_menu_groups']);
    }

    /**
     * Add short labels for languages (e.g. de_CH becomes DE).
     *
     * @param array $languages
     * @return array
     */
    protected function addShortLabels(array $languages)
    {
        foreach ($languages as $key => $val) {
            if (!array_key_exists('short_label', $val) || !$val['short_label']) {
                $languages[$key]['short_label'] = substr(strtoupper($val['label']), 0, 2);
            }
        }

        return $languages;
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation(
            $config['class']['page'],
            'mapManyToOne',
            array(
                'fieldName' => 'parent',
                'targetEntity' => $config['class']['page'],
                'cascade' => array('persist'),
                'inversedBy' => 'children',
                'joinColumns' => array(
                    'name' => 'parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'SET NULL'
                ),
                'orphanRemoval' => false,
            )
        );

        $collector->addAssociation(
            $config['class']['page'],
            'mapOneToMany',
            array(
                'fieldName' => 'children',
                'targetEntity' => $config['class']['page'],
                'mappedBy' => 'parent',
            )
        );

        $collector->addAssociation(
            $config['class']['page'],
            'mapManyToMany',
            array(
                'fieldName' => 'translations',
                'targetEntity' => $config['class']['page'],
                'mappedBy' => 'originals',
            )
        );



        $collector->addAssociation(
            $config['class']['page'],
            'mapManyToMany',
            array(
                'fieldName' => 'originals',
                'targetEntity' => $config['class']['page'],
                'inversedBy'=>"translations",
                'cascade' => array('persist'),
                'joinTable' => array(
                    'name' => 'page_translation',
                    'joinColumns' => array(
                        array(
                            'name' => 'translation_id',
                            'referencedColumnName' => 'id'
                        ),
                    ),
                    'inverseJoinColumns' => array(
                        array(
                            'name' => 'original_id',
                            'referencedColumnName' => 'id'
                        )
                    ),
                )
            )
        );
    }
}
