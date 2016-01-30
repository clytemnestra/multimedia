<?php
namespace Application\Sonata\MediaBundle\Provider;

use Gaufrette\Filesystem;
use Imagine\Image\ImagineInterface;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use \GetId3\GetId3Core as GetId3;


class VideoProvider extends MultimediaBaseProvider
{   
    /**
     * @param string                                                $name
     * @param \Gaufrette\Filesystem                                 $filesystem
     * @param \Sonata\MediaBundle\CDN\CDNInterface                  $cdn
     * @param \Sonata\MediaBundle\Generator\GeneratorInterface      $pathGenerator
     * @param \Sonata\MediaBundle\Thumbnail\ThumbnailInterface      $thumbnail
     * @param array                                                 $allowedExtensions
     * @param array                                                 $allowedMimeTypes
     * @param \Sonata\MediaBundle\Metadata\MetadataBuilderInterface $metadata
     * @param string                                                $root_dir
     */
    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions = array(), array $allowedMimeTypes = array(), ImagineInterface $adapter, MetadataBuilderInterface $metadata = null, $root_dir = '')
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail, $allowedExtensions, $allowedMimeTypes, $adapter, $metadata, $root_dir);
    }
    
    public function getMetadata(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {

            return;
        }
        
        $format = 'reference';
        $getId3 = new GetId3();
        $fileinfo = $getId3
            ->setOptionMD5Data(true)
            ->setOptionMD5DataSource(true)
            ->setEncoding('UTF-8')
            ->analyze($media->getBinaryContent()->getPathname())
        ;
        
        $metadata = array(
            //Propiedades generales del archivo
            'src'      => $this->generatePublicUrl($media, $format),
            'filesize' => $fileinfo['filesize'], 
            'fileformat' => $fileinfo['fileformat'],
            'encoding' => $fileinfo['encoding'],
            'mime_type' => $fileinfo['mime_type'],
            'playtime_seconds' => $fileinfo['playtime_seconds'],
            'playtime_string' => $fileinfo['playtime_string'],
            'bitrate' => $fileinfo['bitrate'],
            
            //Propiedades del audio del Video
            'audio_dataformat' => $fileinfo['audio']['dataformat'],
            'audio_codec' => isset($fileinfo['audio']['codec'])?$fileinfo['audio']['codec']:'',
            'audio_sample_rate' => $fileinfo['audio']['sample_rate'],
            'audio_channels' => $fileinfo['audio']['channels'],
            'audio_bits_per_sample' => isset($fileinfo['audio']['bits_per_sample'])?$fileinfo['audio']['bits_per_sample']:'',
            'audio_lossless' => isset($fileinfo['audio']['lossless'])?$fileinfo['audio']['lossless']:'',
            'audio_channelmode' => isset($fileinfo['audio']['channelmode'])?$fileinfo['audio']['channelmode']:'',
            
            //Propiedades del Video
            'video_dataformat' => $fileinfo['video']['dataformat'],
            'video_resolution_x' => $fileinfo['video']['resolution_x'],
            'video_resolution_y' => $fileinfo['video']['resolution_y'],
            'video_fourcc' => isset($fileinfo['video']['fourcc'])?$fileinfo['video']['fourcc']:'',
            'video_frame_rate' => $fileinfo['video']['frame_rate'],
            'video_codec' => isset($fileinfo['video']['codec'])?$fileinfo['video']['codec']:'',
            
        );       
        
        /*echo "<pre>";
        print_r($metadata);
        print_r($fileinfo);
        echo "</pre>";
        exit();*/

        return $metadata;
    }
    
    public function prePersist(MediaInterface $media)
    {
        // retrieve metadata
        $metadata = $this->getMetadata($media);
        
        // store provider information
        $media->setProviderName($this->name);
        $media->setProviderMetadata($metadata);
        $media->setName($media->getName());
        $media->setHeight($media->getMetadataValue('video_resolution_x'));
        $media->setWidth($media->getMetadataValue('video_resolution_y'));
        $media->setLength($media->getMetadataValue('playtime_seconds'));
        $media->setProviderStatus(MediaInterface::STATUS_OK);
        $media->setCreatedAt(new \Datetime());
        $media->setUpdatedAt(new \Datetime());
    }
    
     /**
     * {@inheritdoc}
     */
    public function getHelperProperties(MediaInterface $media, $format, $options = array())
    {
        /*$getId3 = new GetId3();
        $audio = $getId3
            ->setOptionMD5Data(true)
            ->setOptionMD5DataSource(true)
            ->setEncoding('UTF-8')
            ->analyze($this->root_dir . '/../web' .$this->generatePublicUrl($media, $format))
        ;*/
        
        return array_merge(array(
            'name' => $media->getName(),
            'src' => $this->generatePublicUrl($media, $format),
            'filesize' => $media->getMetadataValue('filesize'),
            'fileformat' => $media->getMetadataValue('fileformat'),
            'encoding' => $media->getMetadataValue('encoding'),
            'mime_type' => $media->getMetadataValue('mime_type'),
            'playtime_seconds' => $media->getMetadataValue('playtime_seconds'),
            'playtime_string' => $media->getMetadataValue('playtime_string'),
            'bitrate' => $media->getMetadataValue('bitrate'),
            'audio_dataformat' => $media->getMetadataValue('audio_dataformat'),
            'audio_codec' => $media->getMetadataValue('audio_codec'),
            'audio_sample_rate' => $media->getMetadataValue('audio_sample_rate'),
            'audio_channels' => $media->getMetadataValue('audio_channels'),
            'audio_bits_per_sample' => $media->getMetadataValue('audio_bits_per_sample'),
            'audio_lossless' => $media->getMetadataValue('audio_lossless'),
            'audio_channelmode' => $media->getMetadataValue('audio_channelmode'),
            'video_dataformat' => $media->getMetadataValue('video_dataformat'),            
            'video_resolution_x' => $media->getMetadataValue('video_resolution_x'),
            'video_resolution_y' => $media->getMetadataValue('video_resolution_y'),
            'video_fourcc' => $media->getMetadataValue('video_fourcc'),
            'video_frame_rate' => $media->getMetadataValue('video_frame_rate'),
            'video_codec' => $media->getMetadataValue('video_codec'),
        ), $options);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getProviderMetadata()
    {
        //$path = 'sonatamedia/files/admin/volume-24-20.png';
        //return new Metadata($this->getName(), $this->getName().'.description', $this->getCdn()->getPath($path, false), 'SonataMediaBundle', array('class' => 'fa fa-file-text-o'));
        return new Metadata($this->getName(), $this->getName().'.description', false, 'SonataMediaBundle', array('class' => 'glyphicon glyphicon-facetime-video'));
    }
}