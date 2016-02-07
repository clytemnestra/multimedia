<?php
namespace Application\Sonata\MediaBundle\Form\Type;

use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class TelType extends AbstractType
{
    /**
     * @author  Joe Sexton <joe@webtipblog.com>
     * @return  string
     */
    public function getName()
    {
        return 'tel';
    }
 
    /**
     * @author  Joe Sexton <joe@webtipblog.com>
     * @return  string
     */
    public function getParent()
    {
        return 'text';
    }
}

