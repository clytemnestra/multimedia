<?php
namespace Application\Sonata\MediaBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sonata_media');
        
        $this->addProvidersSection($node);

        return $treeBuilder;
    }
    
    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addProvidersSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('providers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('file')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('sonata.media.provider.file')->end()
                                ->scalarNode('resizer')->defaultValue(false)->end()
                                ->scalarNode('filesystem')->defaultValue('sonata.media.filesystem.local')->end()
                                ->scalarNode('cdn')->defaultValue('sonata.media.cdn.server')->end()
                                ->scalarNode('generator')->defaultValue('sonata.media.generator.default')->end()
                                ->scalarNode('thumbnail')->defaultValue('sonata.media.thumbnail.format')->end()
                                ->arrayNode('allowed_extensions')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array(
                                        'pdf', 'txt', 'rtf',
                                        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
                                        'odt', 'odg', 'odp', 'ods', 'odc', 'odf', 'odb',
                                        'csv',
                                        'xml',
                                    ))
                                ->end()
                                ->arrayNode('allowed_mime_types')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array(
                                        'application/pdf', 'application/x-pdf', 'application/rtf', 'text/html', 'text/rtf', 'text/plain',
                                        'application/excel', 'application/msword', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint',
                                        'application/vnd.ms-powerpoint', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.graphics', 'application/vnd.oasis.opendocument.presentation', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.chart', 'application/vnd.oasis.opendocument.formula', 'application/vnd.oasis.opendocument.database', 'application/vnd.oasis.opendocument.image',
                                        'text/comma-separated-values',
                                        'text/xml', 'application/xml',
                                        'application/zip', // seems to be used for xlsx document ...
                                    ))
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('sonata.media.provider.image')->end()
                                ->scalarNode('resizer')->defaultValue('sonata.media.resizer.simple')->end()
                                ->scalarNode('filesystem')->defaultValue('sonata.media.filesystem.local')->end()
                                ->scalarNode('cdn')->defaultValue('sonata.media.cdn.server')->end()
                                ->scalarNode('generator')->defaultValue('sonata.media.generator.default')->end()
                                ->scalarNode('thumbnail')->defaultValue('sonata.media.thumbnail.format')->end()
                                ->scalarNode('adapter')->defaultValue('sonata.media.adapter.image.gd')->end()
                                ->arrayNode('allowed_extensions')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('jpg', 'png', 'jpeg'))
                                ->end()
                                ->arrayNode('allowed_mime_types')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array(
                                        'image/pjpeg',
                                        'image/jpeg',
                                        'image/png',
                                        'image/x-png',
                                    ))
                                ->end()
                            ->end()
                        ->end()
                
                        ->arrayNode('audio')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('sonata.media.provider.audio')->end()
                                ->scalarNode('resizer')->defaultValue('sonata.media.resizer.simple')->end()
                                ->scalarNode('filesystem')->defaultValue('sonata.media.filesystem.local')->end()
                                ->scalarNode('cdn')->defaultValue('sonata.media.cdn.server')->end()
                                ->scalarNode('generator')->defaultValue('sonata.media.generator.default')->end()
                                ->scalarNode('thumbnail')->defaultValue('sonata.media.thumbnail.format')->end()
                                ->scalarNode('adapter')->defaultValue('sonata.media.adapter.image.gd')->end()
                                ->arrayNode('allowed_extensions')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('au','snd','mid','rmi','mp4','mp3','aif','aifc','aiff','m3u','ra','ram','Ogg','Linear','PCM','Vorbis','wav','m3u','mp2','s3m','tsi','flac'))
                                ->end()
                                ->arrayNode('allowed_mime_types')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('audio/basic','audio/x-au','audio/mid','auido/L24','audio/mp4','audio/x-aiff','audio/aiff','audio/x-mpegurl','audio/vnd.rn-realaudio','audio/ogg','audio/vorbis','audio/vnd.wav','audio/mpeg','audio/x-mpeg-3','audio/x-mpeg','audio/mpeg3','audio/mod','audio/x-mpequrl','audio/x-mid','audio/x-midi','audio/x-pn-realaudio','audio/x-pn-realaudio-plugin','audio/x-realaudio','audio/s3m','audio/tsp-audio','audio/x-wav','audio/flac', 'audio/x-flac'))
                                ->end()
                            ->end()
                        ->end()
                
                        ->arrayNode('video')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('sonata.media.provider.video')->end()
                                ->scalarNode('resizer')->defaultValue('sonata.media.resizer.simple')->end()
                                ->scalarNode('filesystem')->defaultValue('sonata.media.filesystem.local')->end()
                                ->scalarNode('cdn')->defaultValue('sonata.media.cdn.server')->end()
                                ->scalarNode('generator')->defaultValue('sonata.media.generator.default')->end()
                                ->scalarNode('thumbnail')->defaultValue('sonata.media.thumbnail.format')->end()
                                ->scalarNode('adapter')->defaultValue('sonata.media.adapter.image.gd')->end()
                                ->arrayNode('allowed_extensions')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('mp4','avi', 'divx', 'mkv', 'flv'))
                                ->end()
                                ->arrayNode('allowed_mime_types')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('video/mp4','vieo/mpeg', 'video/x-msvideo', 'video/x-matroska', 'video/flv', 'video/x-flv'))
                                ->end()
                            ->end()
                        ->end()

                
                
                        ->arrayNode('multipleupload')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('sonata.media.provider.multipleupload')->end()
                                ->scalarNode('resizer')->defaultValue('sonata.media.resizer.simple')->end()
                                ->scalarNode('filesystem')->defaultValue('sonata.media.filesystem.local')->end()
                                ->scalarNode('cdn')->defaultValue('sonata.media.cdn.server')->end()
                                ->scalarNode('generator')->defaultValue('sonata.media.generator.default')->end()
                                ->scalarNode('thumbnail')->defaultValue('sonata.media.thumbnail.format')->end()
                                ->scalarNode('adapter')->defaultValue('sonata.media.adapter.image.gd')->end()
                                ->arrayNode('allowed_extensions')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('jpg', 'png', 'jpeg'))
                                ->end()
                                ->arrayNode('allowed_mime_types')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array(
                                        'image/pjpeg',
                                        'image/jpeg',
                                        'image/png',
                                        'image/x-png',
                                    ))
                                ->end()
                            ->end()
                        ->end()
                
                        ->arrayNode('youtube')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('sonata.media.provider.youtube')->end()
                                ->scalarNode('resizer')->defaultValue('sonata.media.resizer.simple')->end()
                                ->scalarNode('filesystem')->defaultValue('sonata.media.filesystem.local')->end()
                                ->scalarNode('cdn')->defaultValue('sonata.media.cdn.server')->end()
                                ->scalarNode('generator')->defaultValue('sonata.media.generator.default')->end()
                                ->scalarNode('thumbnail')->defaultValue('sonata.media.thumbnail.format')->end()
                                ->scalarNode('html5')->defaultValue(false)->end()
                            ->end()
                        ->end()

                        ->arrayNode('dailymotion')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('sonata.media.provider.dailymotion')->end()
                                ->scalarNode('resizer')->defaultValue('sonata.media.resizer.simple')->end()
                                ->scalarNode('filesystem')->defaultValue('sonata.media.filesystem.local')->end()
                                ->scalarNode('cdn')->defaultValue('sonata.media.cdn.server')->end()
                                ->scalarNode('generator')->defaultValue('sonata.media.generator.default')->end()
                                ->scalarNode('thumbnail')->defaultValue('sonata.media.thumbnail.format')->end()
                            ->end()
                        ->end()

                        ->arrayNode('vimeo')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('service')->defaultValue('sonata.media.provider.vimeo')->end()
                                ->scalarNode('resizer')->defaultValue('sonata.media.resizer.simple')->end()
                                ->scalarNode('filesystem')->defaultValue('sonata.media.filesystem.local')->end()
                                ->scalarNode('cdn')->defaultValue('sonata.media.cdn.server')->end()
                                ->scalarNode('generator')->defaultValue('sonata.media.generator.default')->end()
                                ->scalarNode('thumbnail')->defaultValue('sonata.media.thumbnail.format')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}