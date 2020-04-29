<?php
namespace BlockOptions\Site\BlockLayout;

use BlockOptions\Form\Element\RegionMenuSelect;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\View\Renderer\PhpRenderer;

class Slideshow extends AbstractBlockLayout
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
        return 'Slideshow'; // @translate
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
        $title->setLabel('Slideshow Title (optional)');

        $introductionField = new Textarea("o:block[__blockIndex__][o:data][introduction]");
        $introductionField->setLabel('Introductory Text (optional)');
        $introductionField->setAttribute('class', 'block-introduction full wysiwyg');
        $introductionField->setAttribute('rows',20);

        $region = new RegionMenuSelect();

        if ($block) {
            $title->setAttribute('value',$block->dataValue('title'));
            $introductionField->setAttribute('value', $block->dataValue('introduction'));
            $region->setAttribute('value', $block->dataValue('region'));
        }

        $html = $view->formRow($title);
        $html .= $view->blockAttachmentsForm($block);
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($introductionField);
        $html .= $view->formRow($region);
        $html .= $view->blockShowTitleSelect($block);
        $html .= '</div>';
        return $html;
    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headLink()->appendStylesheet($view->basePath('modules/BlockOptions/node_modules/slick-carousel/slick/slick.css'));
        $view->headScript()->appendFile($view->basePath('modules/BlockOptions/node_modules/slick-carousel/slick/slick.min.js'));
        $view->headScript()->appendFile($view->assetUrl('js/slideshow.js', 'BlockOptions'));
        $view->headScript()->appendFile($view->assetUrl('js/regional_html_handler.js', 'BlockOptions'));
    }


    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $attachments = $block->attachments();
        if (!$attachments) {
            return '';
        }

        $data = $block->data();
        $showTitleOption = $block->dataValue('show_title_option', 'item_title');
        list($scope,$region) = explode(':',$data['region']);
        $thumbnailType = $region == 'splash' ? 'splash' : 'large'; // Note “splash” is a custom image size and needs to be configured in config/local.config.php

        $image_attachments = [];
        $audio_attachment = null;

        foreach($attachments as $attachment) {
            $item = $attachment->item();
            $media = $attachment->media() ?: $item->primaryMedia();

            // Filter for media type. $media->mediaType() returns a MIME type.

            if ($media) {
                if (strpos($media->mediaType(),'image') !== false) {
                    $image_attachments[] = $attachment;
                } elseif (strpos($media->mediaType(),'audio') !== false && $audio_attachment == null) {
                    $audio_attachment = $attachment;
                }
            }
        }

        return $view->partial('common/block-layout/slideshow', [
            'block' => $block,
            'useTitleSlide' => !empty($data['title']),
            'titleSlideAttachment' => $image_attachments[0],
            'titleSlideItem' => $image_attachments[0]->item(),
            'titleSlideMedia' => $image_attachments[0]->media() ?: $image_attachments[0]->primaryMedia(),
            'titleSlideTitle' => $data['title'],
            'titleSlideIntro' => $data['introduction'],
            'attachments' => $image_attachments,
            'thumbnailType' => $thumbnailType,
            'showTitleOption' => $showTitleOption,
            'blockId' => $block->id(),
            'regionClass' => 'region-' . $region,
            'targetID' => '#' . $region,
            'hasAudioAttachment' => $audio_attachment != null,
            'audioAttachment' => $audio_attachment
        ]);
    }
}
