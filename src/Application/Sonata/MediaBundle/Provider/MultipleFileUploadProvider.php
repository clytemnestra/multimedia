<?php
namespace Application\Sonata\MediaBundle\Provider;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Provider\ImageProvider as BaseImageProvider;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

use Application\Sonata\MediaBundle\Form\Type\GenderType;
use Application\Sonata\MediaBundle\Form\Type\TelType;
use Application\Sonata\MediaBundle\Form\Type\MultipleType;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\File\File;




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

class MultipleFileUploadProvider extends BaseImageProvider{
    
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
        $formMapper->add('enabled', null, array('required' => false));
        $formMapper->add('cdnIsFlushable');
        $formMapper->add('binaryContent', 'file', array('required' => false));
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper)
    {
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
    /*public function buildCreateForm(FormMapper $formMapper)
    {        
        $formMapper
                ->tab('Post')
                    ->with('Content')
                        ->add('binaryContent', 'file',
                                    array(
                                        'multiple' => true, 
                                        'required' => true, 
                                        'label' => 'Imágen',
                                        'attr' => array(
                                            'data-url' => '/uplodad/media',
                                        )
                                    ), array(
                                    'constraints' => array(
                                        new NotBlank(),
                                        new NotNull(),
                                    )
                                )
                        )
                    ->end()
                ->end()
                ->tab('Publish Options')
                    ->with('')
                        ->add('title', 'text', array("required" => true))
                        ->add('keywords', 'text')
                        //->add('myfieldname', 'myfield')
                    ->end()
                ->end();
    }*/
    
    /*public function getFormTheme() {
        return array('ApplicationSonataMediaBundle:Admin:myfield_edit.html.twig');
    }*/
    
    /**
     * @throws \RuntimeException
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     *
     * @return
     */
    protected function fixBinaryContent(MediaInterface $media)
    {
        /*if(isset($media->getBinaryContent()[0])){
            $media->setBinaryContent() = $media->getBinaryContent()[0];
        }*/
        
        if ($media->getBinaryContent()[0] === null) {
            return;
        }

        // if the binary content is a filename => convert to a valid File
        if (!$media->getBinaryContent()[0] instanceof File) {
            if (!is_file($media->getBinaryContent()[0])) {
                throw new \RuntimeException('The file does not exist : '.$media->getBinaryContent()[0]);
            }
            
            $binaryContent = new File($media->getBinaryContent()[0]);
            $media->setBinaryContent($binaryContent);
        }
    }
    
    /**
     * @throws \RuntimeException
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     */
    protected function fixFilename(MediaInterface $media)
    {
        if ($media->getBinaryContent()[0] instanceof UploadedFile) {
            $media->setName($media->getName() ?: $media->getBinaryContent()[0]->getClientOriginalName());
            $media->setMetadataValue('filename', $media->getBinaryContent()[0]->getClientOriginalName());
        } elseif ($media->getBinaryContent()[0] instanceof File) {
            $media->setName($media->getName() ?: $media->getBinaryContent()[0]->getBasename());
            $media->setMetadataValue('filename', $media->getBinaryContent()[0]->getBasename());
        }

        // this is the original name
        if (!$media->getName()) {
            throw new \RuntimeException('Please define a valid media\'s name');
        }
    }
    
    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     *
     * @return string
     */
    protected function generateReferenceName(MediaInterface $media)
    {
        return sha1($media->getName().rand(11111, 99999)).'.'.$media->getBinaryContent()[0]->guessExtension();
    }
    
    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, MediaInterface $media)
    {
        if (!$media->getBinaryContent()[0] instanceof \SplFileInfo) {
            return;
        }

        if ($media->getBinaryContent()[0] instanceof UploadedFile) {
            $fileName = $media->getBinaryContent()[0]->getClientOriginalName();
        } elseif ($media->getBinaryContent()[0] instanceof File) {
            $fileName = $media->getBinaryContent()[0]->getFilename();
        } else {
            throw new \RuntimeException(sprintf('Invalid binary content type: %s', get_class($media->getBinaryContent()[0])));
        }

        if (!in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $this->allowedExtensions)) {
            $errorElement
                ->with('binaryContent')
                    ->addViolation('Invalid extensions')
                ->end();
        }

        if (!in_array($media->getBinaryContent()[0]->getMimeType(), $this->allowedMimeTypes)) {
            $errorElement
                ->with('binaryContent')
                    ->addViolation('Invalid mime type : '.$media->getBinaryContent()[0]->getMimeType())
                ->end();
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function doTransform(MediaInterface $media)
    {
        $this->fixBinaryContent($media);
        $this->fixFilename($media);

        // this is the name used to store the file
        if (!$media->getProviderReference()) {
            $media->setProviderReference($this->generateReferenceName($media));
        }

        if ($media->getBinaryContent()[0]) {
            $media->setContentType($media->getBinaryContent()[0]->getMimeType());
            $media->setSize($media->getBinaryContent()[0]->getSize());
        }

        $media->setProviderStatus(MediaInterface::STATUS_OK);
    }
    
    /**
     * Set the file contents for an image.
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @param string                                   $contents path to contents, defaults to MediaInterface BinaryContent
     */
    protected function setFileContents(MediaInterface $media, $contents = null)
    {
        $file = $this->getFilesystem()->get(sprintf('%s/%s', $this->generatePath($media), $media->getProviderReference()), true);

        if (!$contents) {
            $contents = $media->getBinaryContent()[0]->getRealPath();
        }

        $metadata = $this->metadata ? $this->metadata->get($media, $file->getName()) : array();
        $file->setContent(file_get_contents($contents), $metadata);
    }
}
