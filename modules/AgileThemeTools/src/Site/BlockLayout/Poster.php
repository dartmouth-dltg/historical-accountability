<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\PosterSchemeSelect;
use AgileThemeTools\Form\Element\PosterTextTreatmentSelect;
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


class Poster extends AbstractBlockLayout
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
        return 'Poster'; // @translate
    }

    public function __construct(HtmlPurifier $htmlPurifier, FormElementManager $formElementManager)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
    }
    
    
    
    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $html = isset($data['html']) ? $this->htmlPurifier->purify($data['html']) : '';
        $data['html'] = $html;
        $block->setData($data);
    }
    


    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $region = new RegionMenuSelect();
        $scheme = new PosterSchemeSelect();
        $textTreatment = new PosterTextTreatmentSelect();


        $textarea = new Textarea("o:block[__blockIndex__][o:data][html]");
        $textarea->setAttribute('class', 'block-html full wysiwyg');
        $textarea->setAttribute('rows',20);


        if ($block) {
            $scheme->setAttribute('value', $block->dataValue('scheme'));
            $textTreatment->setAttribute('value', $block->dataValue('treatment'));
            $region->setAttribute('value', $block->dataValue('region'));
            $textarea->setAttribute('value', $block->dataValue('html'));
        }


        $html = '';
        $html .= $view->formRow($textarea);
        $html .= $view->blockAttachmentsForm($block);
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($scheme);
        $html .= $view->formRow($textTreatment);
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
        $attachments = $block->attachments();

        $data = $block->data();
        list($scope,$region) = explode(':',$data['region']);
        list($schemeScope,$scheme) = explode(':',$data['scheme']);
        list($treatmentScope,$textTreatment) = explode(':',$data['treatment']);

        $thumbnailType = $region == 'splash' ? 'splash' : 'large'; // Note “splash” is a custom image size and needs to be configured in config/local.config.php

        return $view->partial('common/block-layout/poster', [
            'block' => $block,
            'attachment' => $attachments ? $attachments[0] : false,
            'html' => $data['html'],
            'thumbnailType' => $thumbnailType,
            'regionClass' => 'region-' . $region,
            'colourScheme' => 'colour-' .  $scheme,
            'textTreatment' => 'text-' . $textTreatment,
            'targetID' => '#' . $region
        ]);
    }
}