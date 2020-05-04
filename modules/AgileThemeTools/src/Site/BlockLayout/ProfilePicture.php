<?php
namespace AgileThemeTools\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Stdlib\HtmlPurifier;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\View\Renderer\PhpRenderer;
use AgileThemeTools\Form\Element\RegionMenuSelect;
use Zend\Form\Element\Text;
use Omeka\Stdlib\ErrorStore;
use Omeka\Entity\SitePageBlock;


class ProfilePicture extends AbstractBlockLayout
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
        return 'Profile Picture'; // @translate
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
    }
    


    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $region = new RegionMenuSelect();
        
        $title = new Text("o:block[__blockIndex__][o:data][title]");
        $title->setAttribute('class', 'block-title');
        $title->setLabel('Person’s Name');

        if ($block) {
            $region->setAttribute('value', $block->dataValue('region'));
            $title->setAttribute('value',$block->dataValue('title'));
        }


        $html = '';
        $html .= $view->formRow($title);
        $html .= $view->blockAttachmentsForm($block);
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($region);
        $html .= '</div>';
        return $html;
    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headScript()->appendFile($view->assetUrl('js/regional_html_handler.js', 'AgileThemeTools'));
    }


    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $attachments = $block->attachments();
        if (!$attachments) {
            return '';
        }

        $data = $block->data();
        list($scope,$region) = explode(':',$data['region']);
        $thumbnailType = $region == 'splash' ? 'splash' : 'large'; // Note “splash” is a custom image size and needs to be configured in config/local.config.php

        return $view->partial('common/block-layout/profile-picture', [
            'block' => $block,
            'attachment' => $attachments[0],
            'title' => !empty($data['title']) ? $data['title'] : '',
            'thumbnailType' => $thumbnailType,
            'regionClass' => 'region-' . $region,
            'targetID' => '#' . $region

        ]);
    }
}