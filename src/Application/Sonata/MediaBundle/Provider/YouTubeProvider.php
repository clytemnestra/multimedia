<?php
namespace Application\Sonata\MediaBundle\Provider;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Provider\YouTubeProvider as BaseYouTubeProvider;

class YouTubeProvider extends BaseYouTubeProvider
{
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
}
