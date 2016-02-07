<?php
namespace Application\Sonata\MediaBundle\Provider;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Provider\ImageProvider as BaseImageProvider;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

use Application\Sonata\MediaBundle\Form\Type\GenderType;
use Application\Sonata\MediaBundle\Form\Type\TelType;
use Application\Sonata\MediaBundle\Form\Type\MultipleType;


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
}
