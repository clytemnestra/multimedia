<?php
namespace Application\Sonata\MediaBundle\Controller;

use Sonata\MediaBundle\Controller\MediaAdminController as BaseMediaAdminController;

class MediaAdminController extends BaseMediaAdminController
{
    /**
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\Response
     */
    /*public function createAction()
    {
        if($this->getRequest()->get('provider') === 'sonata.media.provider.multipleupload'){
            if(is_array($this->get('request')->request->all())){
                $token = key($this->get('request')->request->all());
                $auxBinaryContent = $this->get('request')->files->get($token);
                //Obtenemos el file
                if(is_array($auxBinaryContent['binaryContent'])){
                    $auxBinaryContent['binaryContent'] = $auxBinaryContent['binaryContent'][0];
                    $this->get('request')->files->set($token, $auxBinaryContent);
                }
            }
        }

        return parent::createAction();
    }*/
    
    /**
     * Create action.
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function createAction()
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }
        
        //Si el provider es 'sonata.media.provider.multipleupload' modificamos el request
        if($this->getRequest()->get('provider') === 'sonata.media.provider.multipleupload'){
            if(is_array($this->get('request')->request->all())){
                $token = key($this->get('request')->request->all());
                $auxBinaryContent = $this->get('request')->files->get($token);
                //Obtenemos el file
                if(is_array($auxBinaryContent['binaryContent'])){
                    $auxBinaryContent['binaryContent'] = $auxBinaryContent['binaryContent'][0];
                    $this->get('request')->files->set($token, $auxBinaryContent);
                }
            }
        }
        
        $parameters = $this->admin->getPersistentParameters();

        if (!$parameters['provider']) {
            return $this->render('SonataMediaBundle:MediaAdmin:select_provider.html.twig', array(
                'providers'     => $this->get('sonata.media.pool')->getProvidersByContext($this->get('request')->get('context', $this->get('sonata.media.pool')->getDefaultContext())),
                'base_template' => $this->getBaseTemplate(),
                'admin'         => $this->admin,
                'action'        => 'create',
            ));
        }

        $object = $this->admin->getNewInstance();

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->getRestMethod() == 'POST') {
            $form->submit($this->get('request'));

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                if (false === $this->admin->isGranted('CREATE', $object)) {
                    throw new AccessDeniedException();
                }

                try {
                    $object = $this->admin->create($object);

                    if ($this->isXmlHttpRequest()) {
                        if($this->getRequest()->get('provider') === 'sonata.media.provider.multipleupload'){
                            /*echo "<pre>";
                            print_r($object);
                            echo "</pre>";
                            exit();*/
                            return $this->renderJson(array(
                                'result'   => 'subida',
                                'objectId' => $this->admin->getNormalizedIdentifier($object)
                            ));
                        }else{
                            return $this->renderJson(array(
                                'result'   => 'ok',
                                'objectId' => $this->admin->getNormalizedIdentifier($object),
                            ));
                        }
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->admin->trans(
                            'flash_create_success',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->logModelManagerException($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->admin->trans(
                            'flash_create_error',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form'   => $view,
            'object' => $object,
        ));
    }
}


