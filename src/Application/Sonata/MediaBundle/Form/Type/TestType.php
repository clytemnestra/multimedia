<?php
namespace Application\Sonata\MediaBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class TestType extends AbstractType
{
    /**
     * @author  Joe Sexton <joe@webtipblog.com>
     * @param  	FormBuilderInterface $builder
     * @param  	array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('location', 'tel', array(
            'label' => 'Phone Number',
        ));
    }
}