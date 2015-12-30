<?php
namespace Application\Sonata\MediaBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\MediaBundle\Admin\BaseMediaAdmin as BaseMediaAdmin;

class MediaAdmin extends BaseMediaAdmin {
    
    
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('description')
            ->add('keywords')
            ->add('enabled')
            ->add('size')
        ;
    }
}