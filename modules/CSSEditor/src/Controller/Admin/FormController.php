<?php
namespace CSSEditor\Controller\Admin;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class FormController extends AbstractActionController
{
    public function indexAction()
    {
        $site = $this->currentSite();
        $this->getRequest()->getQuery()->set('site_id', $site->id());
        
        $view = new ViewModel;
        $view->setVariable('site', $site);
        return $view;
    }
}