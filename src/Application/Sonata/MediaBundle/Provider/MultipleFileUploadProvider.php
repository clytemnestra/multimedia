<?php
namespace Application\Sonata\MediaBundle\Provider;

use Sonata\AdminBundle\Form\FormMapper;
//use Sonata\MediaBundle\Provider\ImageProvider as BaseImageProvider;
use Sonata\MediaBundle\Provider\FileProvider as BaseFileProvider;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

use Application\Sonata\MediaBundle\Form\Type\GenderType;
use Application\Sonata\MediaBundle\Form\Type\TelType;
use Application\Sonata\MediaBundle\Form\Type\MultipleType;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\File\File;
use Imagine\Image\ImagineInterface;



use Gaufrette\Filesystem;

use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;

use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MultipleFileUploadProvider extends BaseFileProvider{
    
    protected $imagineAdapter;

    /**
     * @param string                                                $name
     * @param \Gaufrette\Filesystem                                 $filesystem
     * @param \Sonata\MediaBundle\CDN\CDNInterface                  $cdn
     * @param \Sonata\MediaBundle\Generator\GeneratorInterface      $pathGenerator
     * @param \Sonata\MediaBundle\Thumbnail\ThumbnailInterface      $thumbnail
     * @param array                                                 $allowedExtensions
     * @param array                                                 $allowedMimeTypes
     * @param \Imagine\Image\ImagineInterface                       $adapter
     * @param \Sonata\MediaBundle\Metadata\MetadataBuilderInterface $metadata
     */
    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions = array(), array $allowedMimeTypes = array(), ImagineInterface $adapter, MetadataBuilderInterface $metadata = null)
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail, $allowedExtensions, $allowedMimeTypes, $metadata);

        $this->imagineAdapter = $adapter;
    }
    
    /**
     * {@inheritdoc}
     * Se llama para generar el fomulario de edicion de una imagen del Provider en cuestión
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
        $formMapper->add('enabled', null, array('required' => false));
        $formMapper->add('cdnIsFlushable');
        $formMapper->add('binaryContent', 'file', array('required' => false));
    }
    
    /**
     * {@inheritdoc}
     * Se usa para generar el formulario de alta de imagenes de forma multiple
     */
    public function buildCreateForm(FormMapper $formMapper)
    {
        //echo "function buildCreateForm()<br>";
        $formMapper
            /*
            ->add('binaryContent', 'file', 
                array(
                    'multiple' => true, 
                    'required' => true, 
                    'label' => 'Imágen',
                    'attr' => array(
                        'data-url' => '//jquery-file-upload.appspot.com/',
                    )
                ),
                array(
                    'constraints' => array(
                        new NotBlank(),
                        new NotNull(),
                    ),
                )
            )
                
            */
            
            //->add('author', 'sonata_type_model', array(), array('edit' => 'list'))
            //->add('media', 'sonata_media_type', ['label' => false, 'provider' =>  'sonata.media.provider.multipleupload', 'context' => 'default'])
            // ->add('authorName', null, array('required' => false))
            
                
            //->add('location',  new TelType(), array('label' => 'Phone Number'))
            //  ->add('location', 'tel', array('label' => 'Phone Number'))
            //->add('keywords', MultipleType::class, array('label' => 'Attach images'))
            ->add('binaryContent', 'multiple', 
                array(
                    'label' => '',
                    'multiple' => true, 
                    'required' => false, 
                    'label' => 'Imágen',
                    'attr' => array(
                        'data-url' => '//jquery-file-upload.appspot.com/',
                        'id' => 'fileupload', // no funciona
                        'class' => 'fileupload', // append the class
                    )
                ),
                array(
                    'constraints' => array(
                        new NotBlank(),
                        new NotNull(),
                    ),
                )
            )
                
            // ->add('keywords', 'sonata_media_type', ['label' => false, 'provider' =>  'sonata.media.provider.multipleupload', 'context' => 'default'])    
            /*
             * Collecciones
            ->add('title', 'collection', array(
                 'type' => 'sonata_media_type',
                 'options' => array(
                     'provider' => 'sonata.media.provider.multipleupload',
                     'context'  => 'default',
                     'empty_on_new' => true,
                 ),  
                 'allow_add' => true,
                 'by_reference' => false,
             ))*/ 
            ;
            /*->add(
                    'media', 
                    'sonata_media_type', 
                    array(
                        'provider' => 'sonata.media.provider.multipleupload',
                        'context'  => 'default'
                    )
                )*/
                
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getProviderMetadata()
    {
        return new Metadata($this->getName(), $this->getName().'.description', false, 'ApplicationSonataMediaBundle', array('class' => 'fa fa-picture-o'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHelperProperties(MediaInterface $media, $format, $options = array())
    {
        if ($format == 'reference') {
            $box = $media->getBox();
        } else {
            $resizerFormat = $this->getFormat($format);
            if ($resizerFormat === false) {
                throw new \RuntimeException(sprintf('The image format "%s" is not defined.
                        Is the format registered in your ``sonata_media`` configuration?', $format));
            }

            $box = $this->resizer->getBox($media, $resizerFormat);
        }

        return array_merge(array(
            'alt'      => $media->getName(),
            'title'    => $media->getName(),
            'src'      => $this->generatePublicUrl($media, $format),
            'width'    => $box->getWidth(),
            'height'   => $box->getHeight(),
        ), $options);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getReferenceImage(MediaInterface $media)
    {
        return sprintf('%s/%s',
            $this->generatePath($media),
            $media->getProviderReference()
        );
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
    protected function doTransform(MediaInterface $media)
    {
        parent::doTransform($media);
        
        if (!is_object($media->getBinaryContent()) && !$media->getBinaryContent()) {
            return;
        }

        try {
            $image = $this->imagineAdapter->open($media->getBinaryContent()->getPathname());
        } catch (\RuntimeException $e) {
            $media->setProviderStatus(MediaInterface::STATUS_ERROR);

            return;
        }

        $size  = $image->getSize();

        $media->setWidth($size->getWidth());
        $media->setHeight($size->getHeight());

        $media->setProviderStatus(MediaInterface::STATUS_OK);
    }
    
    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaInterface $media, $format)
    {
        if ($format == 'reference') {
            $path = $this->getReferenceImage($media);
        } else {
            $path = $this->thumbnail->generatePublicUrl($this, $media, $format);
        }

        return $this->getCdn()->getPath($path, $media->getCdnIsFlushable());
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrivateUrl(MediaInterface $media, $format)
    {
        return $this->thumbnail->generatePrivateUrl($this, $media, $format);
    }
}
