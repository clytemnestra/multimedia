<?php
namespace Application\Sonata\MediaBundle\Form\Type;
use Sonata\MediaBundle\Form\Type as BaseType;

class MediaType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => $this->class,
            'provider'      => null,
            'context'       => null,
            'empty_on_new'  => true,
            'new_on_update' => true,
            'allow_extra_fields' => true
        ));
    }
}