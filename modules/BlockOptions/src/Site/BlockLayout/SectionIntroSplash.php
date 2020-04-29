<?php
namespace BlockOptions\Site\BlockLayout;

use BlockOptions\Form\Element\RegionMenuSelect;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Zend\Form\Element\Checkbox;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\View\Renderer\PhpRenderer;

class SectionIntroSplash extends AbstractBlockLayout
{

    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;
    /**
     * @var FormElementManager
     */
    protected $formElementManager;

    public function __construct(HtmlPurifier $htmlPurifier, FormElementManager $formElementManager)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
    }

    public function getLabel()
    {
        return 'Section Introduction Splash'; // @translate
    }

    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $data['introduction'] = isset($data['introduction']) ? $this->htmlPurifier->purify($data['introduction']) : '';
        $data['title'] = isset($data['title']) ? $this->htmlPurifier->purify($data['title']): '';
        $block->setData($data);
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $title = new Text("o:block[__blockIndex__][o:data][title]");
        $title->setAttribute('class', 'block-title');
        $title->setLabel('Section Title');

        $introductionField = new Textarea("o:block[__blockIndex__][o:data][introduction]");
        $introductionField->setLabel('Section Intro Text');
        $introductionField->setAttribute('class', 'block-introduction full wysiwyg');
        $introductionField->setAttribute('rows',20);

        $alignmentLeftField = new Checkbox("o:block[__blockIndex__][o:data][alignment]");
        $alignmentLeftField->setLabel('Align Left');
        $alignmentLeftField->setCheckedValue("left");
        $alignmentLeftField->setUncheckedValue("right");

        $region = new RegionMenuSelect();

        if ($block) {
            $title->setAttribute('value',$block->dataValue('title'));
            $introductionField->setAttribute('value', $block->dataValue('introduction'));
            $alignmentLeftField->setAttribute('value', $block->dataValue('alignment'));
            $region->setAttribute('value', $block->dataValue('region'));
        } else {
            $region->setAttribute('value','region:splash');
        }



        $html = $view->formRow($title);
        $html .= $view->blockAttachmentsForm($block);
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($introductionField);
        $html .= $view->formRow($alignmentLeftField);
        $html .= $view->formRow($region);
        $html .= $view->blockShowTitleSelect($block);
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
        if (!$attachments) {
            return '';
        }

        $data = $block->data();
        $thumbnailType = 'large';
        $showTitleOption = $block->dataValue('show_title_option', 'item_title');
        list($scope,$region) = explode(':',$data['region']);

        return $view->partial('common/block-layout/section-intro-splash', [
            'block' => $block,
            'useTitleSlide' => !empty($data['title']) || !empty($data['introduction']),
            'titleSlideAttachment' => $attachments[0],
            'titleSlideItem' => $attachments[0]->item(),
            'titleSlideMedia' => $attachments[0]->media() ?: $attachments[0]->primaryMedia(),
            'titleSlideTitle' => $data['title'],
            'titleSlideIntro' => $data['introduction'],
            'titleSlideTextAlignment' => $data['alignment'],
            'attachments' => $attachments,
            'thumbnailType' => $thumbnailType,
            'showTitleOption' => $showTitleOption,
            'blockId' => $block->id(),
            'regionClass' => 'region-' . $region,
            'targetID' => '#' . $region
        ]);
    }
}
