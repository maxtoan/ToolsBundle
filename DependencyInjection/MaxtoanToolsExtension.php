<?php

/*
 * This file is part of the Maxtoan Tools package.
 * 
 * (c) https://maximosojo.github.io/tools-bundle
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maxtoan\ToolsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

/**
 * MaxtoanToolsExtension
 * @author Máximo Sojo <maxsojo13@gmail.com>
 */
class MaxtoanToolsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {	
        $processor = new Processor();
        $configuration = new Configuration();
        
        $loaderYml = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loaderYml->load('services.yml');
        $loaderYml->load('commands.yml');
        
        $config = $processor->processConfiguration($configuration, $configs);
        if ($config['paginator']['format_array']) {
            $container->setParameter('paginator_format_array', $config['paginator']['format_array']);
        }
        

        if($config['link_generator']['enable'] === true) {
            $loaderYml->load('link_generator.yml');
            $container->setParameter('maxtoan_tools.service.link_generator.color', $config['link_generator']['color']); 
        }

        if($config['mailer']['enable'] === true) {
            $loaderYml->load('mailer.yml');
            $container->setParameter("maxtoan_tools.swiftmailer.mailer_template_class", $config['mailer']["mailer_template_class"]);
            $container->setParameter("maxtoan_tools.swiftmailer.mailer_component_class", $config['mailer']["mailer_component_class"]);
            $container->setParameter("maxtoan_tools.swiftmailer.mailer_repository_manager", $config['mailer']["mailer_repository_manager"]);

            $idManager = $container->getParameter("maxtoan_tools.swiftmailer.mailer_repository_manager");
            $container->setAlias("maxtoan_tools.repository.mailer.em",$idManager);
        }

        if($config['table_prefix']['enable'] === true) {
            $tablePrefix = $config['table_prefix']['prefix'].$config['table_prefix']['prefix_separator'];
            $tableNameLowercase = $config['table_prefix']['name_lowercase'];
            $tablePrefixListerner = new Definition($config['table_prefix']['listerner_class']);
            $tablePrefixListerner
                    ->addArgument($tablePrefix)
                    ->addArgument($tableNameLowercase)
                    ->addTag('doctrine.event_subscriber')
                    ;
            $tablePrefixListerner->addMethodCall("setConfig",array($config['table_prefix']));
            $container->setDefinition('maxtoan_tools.table_prefix_subscriber', $tablePrefixListerner);
        }

        // Command for clear db
        $container->setParameter('maxtoan_tools.command.db_clear_truncate_entities', false);
        $container->setParameter('maxtoan_tools.command.db_clear_delete_entities', false);
        // Command for clear folders
        $container->setParameter('maxtoan_tools.command.folder_clear_clear_paths', false);
        if(isset($config['command'])) {
            if(isset($config['command']['database']['clear']['truncate_entities'])) {
                $container->setParameter('maxtoan_tools.command.db_clear_truncate_entities', $config['command']['database']['clear']['truncate_entities']);
            }
            if(isset($config['command']['database']['clear']['delete_entities'])) {
                $container->setParameter('maxtoan_tools.command.db_clear_delete_entities', $config['command']['database']['clear']['delete_entities']);
            }
            if(isset($config['command']['folder']['clear']['clear_paths'])) {
                $container->setParameter('maxtoan_tools.command.folder_clear_clear_paths', $config['command']['folder']['clear']['clear_paths']);
            }
        }
        
        $container->setParameter('maxtoan_tools.service.link_generator.enable', $config['link_generator']['enable']);
        $container->setParameter('maxtoan_tools.loading.color', $config['loading']['color']);

        if($config['jms_serializer']['enable'] === true) {
            $loaderYml->load('jms_serializer.yml');
        }

        $container->setParameter('maxtoan_tools.object_manager.enable',$config['object_manager']['enable']);
        if($config['object_manager']['enable'] === true){
            $loaderYml->load('services/object_manager.yml');
            unset($config['object_manager']["enable"]);
            $container->setParameter('maxtoan_tools.object_manager',$config['object_manager']);
        }
    }
}