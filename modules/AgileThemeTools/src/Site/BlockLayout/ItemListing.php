<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\View\Renderer\PhpRenderer;

class ItemListing extends AbstractBlockLayout
{
    public function getLabel()
    {
        return 'Item Listing with Introduction'; // @translate
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
        SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $title = new Text("o:block[__blockIndex__][o:data][title]");
        $title->setAttribute('class', 'block-title');
        $title->setLabel('Title');

        $introductionField = new Textarea("o:block[__blockIndex__][o:data][introduction]");
        $introductionField->setLabel('Intro Text');
        $introductionField->setAttribute('class', 'block-introduction full wysiwyg');
        $introductionField->setAttribute('rows',20);

        $buttonText = new Text("o:block[__blockIndex__][o:data][buttonText]");
        $buttonText->setAttribute('class', 'block-button-text');
        $buttonText->setLabel('Button Text (optional)');

        $buttonPath = new Text("o:block[__blockIndex__][o:data][buttonPath]");
        $buttonPath->setAttribute('class', 'block-button-path');
        $buttonPath->setLabel('Button Path (optional)');

        $region = new RegionMenuSelect();

        $classes = new Text("o:block[__blockIndex__][o:data][classes]");
        $classes->setAttribute('class', 'block-button-classes');
        $classes->setLabel('Block Class (optional, separate with spaces)');

        if ($block) {
            $title->setAttribute('value',$block->dataValue('title'));
            $introductionField->setAttribute('value', $block->dataValue('introduction'));
            $buttonPath->setAttribute('value',$block->dataValue('buttonPath'));
            $buttonText->setAttribute('value',$block->dataValue('buttonText'));
            $region->setAttribute('value', $block->dataValue('region'));
            $classes->setAttribute('value',$block->dataValue('classes'));
        } else {
            $region->setAttribute('value','region:default');
        }

        $html = "<p>An expanded Item Showcase with Title and Introductory Text</p>";
        $html .= $view->formRow($title);
        $html .= $view->formRow($introductionField);
        $html .= $view->blockAttachmentsForm($block);
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($buttonText);
        $html .= $view->formRow($buttonPath);
        $html .= $view->formRow($classes);
        $html .= $view->blockThumbnailTypeSelect($block);
        $html .= $view->formRow($region);
        $html .= '</div>';

        return $html;
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $attachments = $block->attachments();
        if (!$attachments) {
            return '';
        }

        $data = $block->data();
        $thumbnailType = $block->dataValue('thumbnail_type', 'square');
        list($scope,$region) = explode(':',$data['region']);

        return $view->partial('common/block-layout/item-listing', [
            'block' => $block,
            'attachments' => $attachments,
            'blockId' => $block->id(),
            'buttonText' => $data['buttonText'],
            'buttonPath' => $data['buttonPath'],
            'title' => $data['title'],
            'introduction' => $data['introduction'],
            'class' => $data['classes'],
            'hasTitle' => !empty($data['title']),
            'hasIntroduction' => !empty($data['introduction']),
            'hasButton' => !empty($data['buttonText']) && !empty($data['buttonPath']),
            'thumbnailType' => $thumbnailType,
            'targetID' => '#' . $region
        ]);
    }
}
