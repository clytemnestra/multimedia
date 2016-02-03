<?php
namespace Application\Sonata\MediaBundle\Provider;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Provider\ImageProvider as BaseImageProvider;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;


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
        $formMapper->add('binaryContent', 'file', 
                array(
                    'multiple' => true, 
                    'required' => true, 
                    'label' => 'Imágen',
                    'attr' => array(
                        'data-url' => '/uplodad/media',
                    )
                ),
                array(
                    'constraints' => array(
                    new NotBlank(),
                    new NotNull(),
                ),
        ));
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
