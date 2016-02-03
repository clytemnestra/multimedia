<?php
namespace Application\Sonata\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;



use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;


#use Sonata\MediaBundle\DependencyInjection\SonataMediaExtension;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class ApplicationSonataMediaExtension extends Extension
{
     /**
     * Loads the url shortener configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container){
        $processor     = new Processor();
        $configuration = new Configuration();
        $config        = $processor->processConfiguration($configuration, $configs);
        
        //parent::load($configs, $container);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('provider.xml');
        $this->configureProviders($container, $config);
    }
    
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function configureProviders(ContainerBuilder $container, $config)
    {
        $container->getDefinition('sonata.media.provider.audio')
            ->replaceArgument(5, array_map('strtolower', $config['providers']['audio']['allowed_extensions']))
            ->replaceArgument(6, $config['providers']['audio']['allowed_mime_types'])
            ->replaceArgument(7, new Reference($config['providers']['audio']['adapter']))
        ;
        
        $container->getDefinition('sonata.media.provider.video')
            ->replaceArgument(5, array_map('strtolower', $config['providers']['video']['allowed_extensions']))
            ->replaceArgument(6, $config['providers']['video']['allowed_mime_types'])
            ->replaceArgument(7, new Reference($config['providers']['video']['adapter']))
        ;
        
        $container->getDefinition('sonata.media.provider.multipleupload')
            ->replaceArgument(5, array_map('strtolower', $config['providers']['multipleupload']['allowed_extensions']))
            ->replaceArgument(6, $config['providers']['multipleupload']['allowed_mime_types'])
            ->replaceArgument(7, new Reference($config['providers']['multipleupload']['adapter']))
        ;
        
        $container->getDefinition('sonata.media.provider.image')
            ->replaceArgument(5, array_map('strtolower', $config['providers']['image']['allowed_extensions']))
            ->replaceArgument(6, $config['providers']['image']['allowed_mime_types'])
            ->replaceArgument(7, new Reference($config['providers']['image']['adapter']))
        ;
    }
}
