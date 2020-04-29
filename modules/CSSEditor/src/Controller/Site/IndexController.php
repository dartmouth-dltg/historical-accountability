<?php
namespace CSSEditor\Controller\Site;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function indexAction() {
        $siteSettings = $this->siteSettings();
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'text/css; charset=utf-8');
        $response->setContent($this->siteSettings()->get('css_editor_css'));
        return $response;
    }
}