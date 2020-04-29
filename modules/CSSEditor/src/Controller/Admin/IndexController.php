<?php

namespace CSSEditor\Controller\Admin;

use Omeka\Mvc\Exception;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Form;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        if (!$this->currentSite()->userIsAllowed('css-editor-modify')) {
            throw new Exception\PermissionDeniedException(
                'User does not have permission to edit CSS' // @translate
            ); 
        }
        $siteSettings = $this->siteSettings();
        $view = new ViewModel();
        $form = $this->getForm(Form::class);
        if ($this->getRequest()->isPost()) {
            $params = $this->params()->fromPost();
            if (isset($params['css'])) {
                $css = $params['css'];
            } else {
                $css = '';
            }
            if (isset($params['external-css'])) {
                $externalCss = array_filter($params['external-css']);
            } else {
                $externalCss = [];
            }
            $siteSettings->set('css_editor_css', $css);
            $siteSettings->set('css_editor_external_css', $externalCss);
            $this->messenger()->addSuccess('CSS successfully updated.'); // @translate
        }
        $css = $siteSettings->get('css_editor_css');
        $externalCss = $siteSettings->get('css_editor_external_css');
        $view->setVariable('form', $form);
        $view->setVariable('css', $css);
        $view->setVariable('externalCss', $externalCss);
        return $view;
    }
}
