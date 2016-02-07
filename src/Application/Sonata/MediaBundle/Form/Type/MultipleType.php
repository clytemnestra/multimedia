<?php
namespace Application\Sonata\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class MultipleType extends AbstractType
{
    /**
     * @author  Esteban Novo <novo.esteban@gmail.com>
     * @return  string
     */
    public function getName()
    {
        return 'multiple';
    }

    public function getParent()
    {
        return FileType::class;
    }
}
