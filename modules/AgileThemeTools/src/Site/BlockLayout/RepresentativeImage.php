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

class RepresentativeImage extends AbstractBlockLayout
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
        return 'Representative Image'; // @translate
    }

    public function __construct(HtmlPurifier $htmlPurifier, FormElementManager $formElementManager)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
    }


    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $region = new RegionMenuSelect();

        if ($block) {
            $region->setAttribute('value', $block->dataValue('region'));
        }


        $html = '<p>A representative Image is used as a thumbnail when this content is linked elsewhere on the site. It does <strong>not</strong> appear on the primary page.';
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
        
        // Title and Url of representative images are contextual and set in SeectionPageListing block layout.

        return $view->partial('common/block-layout/representative-image', [
            'block' => $block,
            'attachment' => $attachments[0],
            'thumbnailType' => $thumbnailType,
        ]);
    }
}