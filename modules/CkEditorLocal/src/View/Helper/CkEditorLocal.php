<?php
namespace CkEditorLocal\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper for loading scripts necessary to use CKEditor on a page.
 */
class CkEditorLocal extends AbstractHelper
{
    /**
     * Load the scripts necessary to use CKEditor on a page.
     */
    public function __invoke()
    {
        $view = $this->getView();
        // @todo: check to see if local config file exists. If not use Omeka’s own.
        $customConfigUrl = $view->escapeJs($view->basePath('config/ckeditor_config.local.js', 'Omeka'));
        $view->headScript()->appendFile($view->assetUrl('vendor/ckeditor/ckeditor.js', 'Omeka'));
        $view->headScript()->appendFile($view->assetUrl('vendor/ckeditor/adapters/jquery.js', 'Omeka'));
        $view->headScript()->appendScript("CKEDITOR.config.customConfig = '$customConfigUrl'");
    }
}
