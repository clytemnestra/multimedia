<?php
namespace Application\Sonata\MediaBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin as BaseMediaAdmin;
#use Sonata\AdminBundle\Route\RouteCollection;

class MediaAdmin extends BaseMediaAdmin {
    
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->addIdentifier('keywords')
            ->add('description')
            ->add('enabled')
            ->add('size')
        ;
    }
    
    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('title')
            ->add('subtitle')
            ->add('location')
            ->add('keywords')
            ->add('providerReference')
            ->add('enabled')
            ->add('context')
        ;

        $providers = array();

        $providerNames = (array) $this->pool->getProviderNamesByContext($this->getPersistentParameter('context', $this->pool->getDefaultContext()));
        foreach ($providerNames as $name) {
            $providers[$name] = $name;
        }

        $datagridMapper->add('providerName', 'doctrine_orm_choice', array(
            'field_options' => array(
                'choices'  => $providers,
                'required' => false,
                'multiple' => false,
                'expanded' => false,
            ),
            'field_type' => 'choice',
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $media = $this->getSubject();       

        if (!$media) {
            $media = $this->getNewInstance();
        }

        if (!$media || !$media->getProviderName()) {
            return;
        }

        $formMapper->getFormBuilder()->addModelTransformer(new ProviderDataTransformer($this->pool, $this->getClass()), true);

        $provider = $this->pool->getProvider($media->getProviderName());

        if ($media->getId()) {
            $provider->buildEditForm($formMapper);
        } else {
            $provider->buildCreateForm($formMapper);
        }
    }
    
    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                if($this->getRequest()->get('provider') === 'sonata.media.provider.multipleupload'){
                    return 'ApplicationSonataMediaBundle:MediaAdmin:edit_multiple.html.twig';
                }else{
                    return parent::getTemplate($name);
                }
                break; 
            default:
                return parent::getTemplate($name);
                break;
        }
    }
    
    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            array('ApplicationSonataMediaBundle:Form:fields.html.twig')
        );
    }
}