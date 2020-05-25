<?php
namespace AgileThemeTools\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Stdlib\HtmlPurifier;
use Zend\Form\Element\Textarea;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\View\Renderer\PhpRenderer;
use AgileThemeTools\Form\Element\RegionMenuSelect;
use Zend\Form\Element\Text;
use Omeka\Stdlib\ErrorStore;
use Omeka\Entity\SitePageBlock;


class Callout extends AbstractBlockLayout
{
    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;
    /**
     * @var FormElementManager
     */
    protected $formElementManager;


    public function getLabel()
    {
        return 'Callout'; // @translate
    }

    public function __construct(HtmlPurifier $htmlPurifier, FormElementManager $formElementManager)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
    }
    
    
    
    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $data['title'] = isset($data['title']) ? $this->htmlPurifier->purify($data['title']) : '';
        $html = isset($data['html']) ? $this->htmlPurifier->purify($data['html']) : '';
        $data['html'] = $html;
        $block->setData($data);
    }
    


    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $region = new RegionMenuSelect();
        

        $textarea = new Textarea("o:block[__blockIndex__][o:data][html]");
        $textarea->setAttribute('class', 'block-html full wysiwyg');
        $textarea->setAttribute('rows',20);

        $callout = new Textarea("o:block[__blockIndex__][o:data][callout]");
        $callout->setAttribute('class', 'block-html full wysiwyg');
        $callout->setAttribute('rows',20);


        if ($block) {
            $region->setAttribute('value', $block->dataValue('region'));
            $textarea->setAttribute('value', $block->dataValue('html'));
            $callout->setAttribute('value', $block->dataValue('callout'));
        }


        $html = '';
        $html .= '<h4>Main Text</h4>';
        $html .= $view->formRow($textarea);
        $html .= '<h4>Callout Text</h4>';
        $html .= $view->formRow($callout);
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($region);
        $html .= '</div>';
        return $html;
    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headScript()->appendFile($view->assetUrl('js/regional_html_handler.js', 'BlockOptions'));
    }


    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {

        $data = $block->data();
        list($scope,$region) = explode(':',$data['region']);
        $thumbnailType = $region == 'splash' ? 'splash' : 'large'; // Note “splash” is a custom image size and needs to be configured in config/local.config.php

        return $view->partial('common/block-layout/callout', [
            'block' => $block,
            'html' => $data['html'],
            'callout' => $data['callout'],
            'thumbnailType' => $thumbnailType,
            'regionClass' => 'region-' . $region,
            'targetID' => '#' . $region

        ]);
    }
}