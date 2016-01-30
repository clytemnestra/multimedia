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

use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Form;


class AudioProvider extends FileProvider
{
    protected $imagineAdapter;
    private $root_dir;
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
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail, $allowedExtensions, $allowedMimeTypes, $metadata);
        $this->imagineAdapter = $adapter;
        $this->root_dir = $root_dir;
        
        /*echo "<pre>";
        print_r($allowedExtensions);
        print_r($allowedMimeTypes);
        
        echo "</pre>";
        exit();*/
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper)
    {
        $formMapper->add('title', 'text', array("required" => true));
        $formMapper->add('subtitle', 'text', array("required" => true));
        $formMapper->add('location');
        $formMapper->add('keywords', 'text', array("required" => true));        
        $formMapper->add('authorName');
        $formMapper->add('copyright');
        $formMapper->add('description');
        $formMapper->add('name');
        //$formMapper->add('duration', 'text', array("required" => true));
        //$formMapper->add('quality', 'text', array("required" => false));
        $formMapper->add('enabled', null, array('required' => false));
        $formMapper->add('cdnIsFlushable');
        $formMapper->add('binaryContent', 'file', array('required' => false));
    }
    
    public function getMetadata(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {

            return;
        }
        
        $format = 'reference';
        $getId3 = new GetId3();
        $audio = $getId3
            ->setOptionMD5Data(true)
            ->setOptionMD5DataSource(true)
            ->setEncoding('UTF-8')
            ->analyze($media->getBinaryContent()->getPathname())
        ;
        
        /*echo "<pre>";
        print_r($audio);
        echo "</pre>";
        exit();*/
        $metadata = array(
            //'src'      => $this->generatePublicUrl($media, $format),
            'image_src' => $this->getMetadataImage($audio),
            'image_mime' => $this->getMetadataImageMimeTypes($audio),
            
            'encoding' => $audio['encoding'],
            'filesize' => $audio['filesize'],            
            'mime_type' => $audio['mime_type'],
            'fileformat' => $audio['fileformat'],
            'playtime_seconds' => $audio['playtime_seconds'],
            'playtime_string' => $audio['playtime_string'],
            
            'dataformat' => $audio['audio']['dataformat'],
            'channels' => $audio['audio']['channels'],
            'sample_rate' => $audio['audio']['sample_rate'],
            'bitrate' => $audio['audio']['bitrate'],
            'channelmode '=> $audio['audio']['channelmode'],
            'bitrate_mode' => $audio['audio']['bitrate_mode'],
            
            
            'title' => $this->getMetadataTitle($audio),
            'comment' => $this->getMetadataComment($audio),
            'artist' => $this->getMetadataArtist($audio),
            'album' => $this->getMetadataAlbum($audio),
            'year' => $this->getMetadataAlbumYear($audio),
            'track_number' => $this->getMetadataTrackNumber($audio),
            'genre' => $this->getMetadataGenre($audio),
            
        );       

        return $metadata;
    }
    
    private function getMetadataGenre($metadata){
        if(isset($metadata['tags_html']['id3v1']['genre'][0])){
            return $metadata['tags_html']['id3v1']['genre'][0];
        }else{
            if(isset($metadata['tags_html']['id3v2']['genre'][0])){
                return $metadata['tags_html']['id3v2']['genre'][0];
            }else{
                return '';
            }
        }
    }
    
    private function getMetadataArtist($metadata){
        if(isset($metadata['tags_html']['id3v2']['artist'][0])){
            return $metadata['tags_html']['id3v2']['artist'][0];
        }else{
            if(isset($metadata['id3v1']['artist'])){
                return $metadata['id3v1']['artist'];
            }else{
                if(isset($metadata['id3v2']['artist'])){
                    return $metadata['id3v2']['artist'];
                }else{
                    return '';
                }
            }
        }
    }
    
    private function getMetadataAlbum($metadata){
        if(isset($metadata['tags_html']['id3v2']['album'][0])){
            return $metadata['tags_html']['id3v2']['album'][0];
        }else{
            if(isset($metadata['id3v1']['album'])){
                return $metadata['id3v1']['album'];
            }else{
                if(isset($metadata['id3v2']['album'])){
                    return $metadata['id3v2']['album'];
                }else{
                    return '';
                }
            }
        }
    }
    
    private function getMetadataAlbumYear($metadata){
        if(isset($metadata['tags_html']['id3v2']['year'][0])){
            return $metadata['tags_html']['id3v2']['year'][0];
        }else{
            if(isset($metadata['id3v1']['year'])){
                return $metadata['id3v1']['year'];
            }else{
                if(isset($metadata['id3v2']['year'])){
                    return $metadata['id3v2']['year'];
                }else{
                    return '';
                }
            }
        }
    }
    
    private function getMetadataTrackNumber($metadata){
        if(isset($metadata['tags_html']['id3v2']['track_number'][0])){
            return $metadata['tags_html']['id3v2']['track_number'][0];
        }else{
            return 0;
        }
    }
    
    private function getMetadataImage($metadata){
        if(isset($metadata['comments']['picture'][0]['data'])){
            return base64_encode($metadata['comments']['picture'][0]['data']);
        }else{
           if(isset($metadata['id3v2']['APIC'][0]['data'])){
               return base64_encode($metadata['id3v2']['APIC'][0]['data']);
           }else{
               return "";
           }
        }
    }
    
    private function getMetadataImageMimeTypes($metadata){
        if(isset($metadata['comments']['picture'][0]['image_mime'])){
            return base64_encode($metadata['comments']['picture'][0]['image_mime']);
        }else{
           if(isset($metadata['id3v2']['APIC'][0]['image_mime'])){
               return base64_encode($metadata['id3v2']['APIC'][0]['image_mime']);
           }else{
               return "";
           }
        }
    }
    
    private function getMetadataTitle($metadata){
        if(isset($metadata['tags_html']['id3v2']['title'][0])){
            return $metadata['tags_html']['id3v2']['title'][0];
        }else{
            if(isset($metadata['tags_html']['id3v1']['title'][0])){
                return $metadata['tags_html']['id3v1']['title'][0];
            }else{
                if(isset($metadata['id3v1']['title'])){
                    return $metadata['id3v1']['title'];
                }else{
                    if(isset($metadata['id3v2']['comments']['title'][0])){
                        return $metadata['id3v2']['comments']['title'][0];
                    }else{
                        if(isset($metadata['tags']['id3v1']['title'][0])){
                            return $metadata['tags']['id3v1']['title'][0];
                        }else{
                            if(isset($metadata['tags']['id3v2']['title'][0])){
                                return $metadata['tags']['id3v2']['title'][0];
                            }else{
                                return '';
                            }
                        }
                    }
                }
            }
        }
    }
    
    private function getMetadataComment($metadata){
        if(isset($metadata['tags_html']['id3v2']['comment'][0])){
            return $metadata['tags_html']['id3v2']['comment'][0];
        }else{
            if(isset($metadata['id3v1']['comment'])){
                return $metadata['id3v1']['comment'];
            }else{
                return '';
            }
        }
    }
    
    public function prePersist(MediaInterface $media)
    {
        // retrieve metadata
        $metadata = $this->getMetadata($media);
        
        // store provider information
        $media->setProviderName($this->name);
        //$media->setProviderReference($media->getBinaryContent());
        //$media->setProviderReference($this->generateReferenceName($media));
        $media->setProviderMetadata($metadata);

        // update Media common field from metadata
        $media->setName($media->getName());
        $media->setTitle($metadata['title']);
        $media->setSubTitle($metadata['album']);
        $media->setDescription($metadata['comment']);
        $media->setAuthorName($metadata['artist']);
        $media->setKeywords("$metadata[artist], $metadata[album], $metadata[title]");
        $media->setHeight(0);
        $media->setWidth(0);
        $media->setLength($metadata['playtime_seconds']);
        //$media->setContentType('video/x-flv');
        //$media->setProviderStatus(Media::STATUS_OK);
        $media->setProviderStatus(MediaInterface::STATUS_OK);

        $media->setCreatedAt(new \Datetime());
        $media->setUpdatedAt(new \Datetime());
    }
    
    public function preUpdate(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {

            return;
        }

        $metadata = $this->getMetadata($media);

        //$media->setProviderReference($media->getBinaryContent());
        //$media->setProviderReference($media->getPreviousProviderReference());
        $media->setProviderMetadata($metadata);
        $media->setHeight($metadata['height']);
        $media->setWidth($metadata['width']);
        $media->setProviderStatus(Media::STATUS_OK);

        $media->setUpdatedAt(new \Datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(MediaInterface $media)
    {
        if ($media->getBinaryContent() === null) {
            return;
        }

        $this->setFileContents($media);

        $this->generateThumbnails($media);
    }
    
    /**
     * {@inheritdoc}
     */
    public function updateMetadata(MediaInterface $media, $force = true)
    {
        try {
            // this is now optimized at all!!!
            $path       = tempnam(sys_get_temp_dir(), 'sonata_update_metadata');
            $fileObject = new \SplFileObject($path, 'w');
            $fileObject->fwrite($this->getReferenceFile($media)->getContent());

            $image = $this->imagineAdapter->open($fileObject->getPathname());
            $size  = $image->getSize();

            $media->setSize($fileObject->getSize());
            $media->setWidth($size->getWidth());
            $media->setHeight($size->getHeight());
        } catch (\LogicException $e) {
            $media->setProviderStatus(MediaInterface::STATUS_ERROR);

            $media->setSize(0);
            $media->setWidth(0);
            $media->setHeight(0);
        }
    }
    
     /**
     * {@inheritdoc}
     */
    public function getHelperProperties(MediaInterface $media, $format, $options = array())
    {
        $getId3 = new GetId3();
        $audio = $getId3
            ->setOptionMD5Data(true)
            ->setOptionMD5DataSource(true)
            ->setEncoding('UTF-8')
            ->analyze($this->root_dir . '/../web' .$this->generatePublicUrl($media, $format))
        ;

        /*if (isset($audio['error'])) {
            throw new \RuntimeException(sprintf('Error at reading audio properties from "%s" with GetId3: %s.', $this->root_dir . '/../web' .$this->generatePublicUrl($media, $format), $audio['error']));
        }*/
        //$this->setLength(isset($audio['playtime_seconds']) ? $audio['playtime_seconds'] : '');
        
        //$audio['comments']['picture'][0]['data'] = base64_encode($audio['comments']['picture'][0]['data']);
        //unset($audio['comments']['picture'][0]['data']);
        //unset($audio['id3v2']['APIC'][0]['data']);
        //print_r($getId3->GetFileFormatArray());
        //print_r($getId3->getInfo());
        
        return array_merge(array(
            'name'      => $media->getName(),
            'src'      => $this->generatePublicUrl($media, 'reference'),
            'image_src'      => $this->getMetadataImage($audio),
            'image_mime'      => $this->getMetadataImageMimeTypes($audio),
            'encoding'     => $audio['encoding'],
            'filesize'     => $audio['filesize'],            
            'mime_type'     => $audio['mime_type'],
            'fileformat'     => $audio['fileformat'],
            'dataformat'      => $audio['audio']['dataformat'],
            'channels'      => $audio['audio']['channels'],
            'sample_rate'      => $audio['audio']['sample_rate'],
            'bitrate'      => $audio['audio']['bitrate'],
            'channelmode'      => $audio['audio']['channelmode'],
            'bitrate_mode'      => $audio['audio']['bitrate_mode'],
            'title'      => $this->getMetadataTitle($audio),
            'comment'      => $this->getMetadataComment($audio),
            'artist'      => $this->getMetadataArtist($audio),
            'album'      => $this->getMetadataAlbum($audio),
            'year'      => $this->getMetadataAlbumYear($audio),
            'track_number'      => $this->getMetadataTrackNumber($audio),
            'genre'      => $this->getMetadataGenre($audio),            
            'playtime_seconds'      => $audio['playtime_seconds'],
            'playtime_string'      => $audio['playtime_string'],
            //'image'      => $media,
            //'format'      => $format,
            //'audio' => $audio,
        ), $options);
    }
    
    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaInterface $media, $format)
    {
        if ($format == 'reference') {
            $path = $this->getReferenceImage($media);
        } else {            
            $arr_metadata = array(
                'image' =>$media->getMetadataValue('image_src'),
                'mime_type' =>$media->getMetadataValue('mime_type'),
                'format' =>$media->getMetadataValue('fileformat')
            );
            /*if(isset($arr_metadata['format'])){
                $path = $arr_metadata;
            }else{*/
                $path = $this->thumbnail->generatePublicUrl($this, $media, $format);
            //}
            
            //$path = $this->getMetadataImage($metadata);
        }
        //return $path;
        //return $media['providerMetadata']['image_src'];
        //return $media->getMetadataValue('image_src');
        //return $media;
        return $this->getCdn()->getPath($path, $media->getCdnIsFlushable());
    }
}