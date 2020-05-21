<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use AgileThemeTools\Form\Element\SectionsMenuSelect;
use AgileThemeTools\Form\Element\SectionListingTemplateSelect;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Omeka\Api\Manager;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Text;
use Zend\View\Renderer\PhpRenderer;
use AgileThemeTools\Controller\SectionManager;


class SectionPageListing extends AbstractBlockLayout
{
    /**
     * @var Manager
     */
    protected $sectionManager;

    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;
    /**
     * @var FormElementManager
     */
    protected $formElementManager;

    protected $blockLayoutManager;

    protected $errorStore;

    protected $site;
    public function __construct(SectionManager $sectionManager, $blockLayoutManager, $htmlPurifier, FormElementManager $formElementManager, ErrorStore $errorStore)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
        $this->sectionManager = $sectionManager;
        $this->blockLayoutManager = $blockLayoutManager;
        $this->errorStore = $errorStore;
        //$this->site = new HBOCurrentSite();
    }

    public function getLabel()
    {
        return 'Section Listing'; // @translate

    }

    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $data['introduction'] = isset($data['introduction']) ? $this->htmlPurifier->purify($data['introduction']) : '';
        $data['title'] = isset($data['title']) ? $this->htmlPurifier->purify($data['title']) : '';
        $data['buttonText'] = isset($data['buttonText']) ? $this->htmlPurifier->purify($data['buttonText']) : '';
        $data['buttonPath'] = isset($data['buttonPath']) ? $this->htmlPurifier->purify($data['buttonPath']) : '';
        $data['classes'] = isset($data['classes']) ? $this->htmlPurifier->purify($data['classes']) : '';
        $data['itemCount'] = isset($data['itemCount']) ? $this->htmlPurifier->purify($data['itemCount']) : '';
        $block->setData($data);
    }



    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null)
    {

        $sectionsMenu = new SectionsMenuSelect();
        $sectionsMenu->setSectionOptions($this->sectionManager->indexAction());

        $title = new Text("o:block[__blockIndex__][o:data][title]");
        $title->setAttribute('class', 'block-title');
        $title->setLabel('Section Title');

        $introductionField = new Textarea("o:block[__blockIndex__][o:data][introduction]");
        $introductionField->setLabel('Section Intro Text');
        $introductionField->setAttribute('class', 'block-introduction full wysiwyg');
        $introductionField->setAttribute('rows',20);

        $buttonText = new Text("o:block[__blockIndex__][o:data][buttonText]");
        $buttonText->setAttribute('class', 'block-button-text');
        $buttonText->setLabel('Button Text (optional)');

        $buttonPath = new Text("o:block[__blockIndex__][o:data][buttonPath]");
        $buttonPath->setAttribute('class', 'block-button-path');
        $buttonPath->setLabel('Button Path (optional)');

        $region = new RegionMenuSelect();
        $template = new SectionListingTemplateSelect();

        $classes = new Text("o:block[__blockIndex__][o:data][classes]");
        $classes->setAttribute('class', 'block-button-classes');
        $classes->setLabel('Block Class (optional, separate with spaces)');

        $itemCount = new Text("o:block[__blockIndex__][o:data][itemCount]");
        $itemCount->setAttribute('class', 'block-button-item-count');
        $itemCount->setLabel('Number of Items to Display (optional, defaults to all)');

        if ($block) {
            $sectionsMenu->setAttribute('value',$block->dataValue('section'));
            $title->setAttribute('value',$block->dataValue('title'));
            $introductionField->setAttribute('value', $block->dataValue('introduction'));
            $buttonPath->setAttribute('value',$block->dataValue('buttonPath'));
            $buttonText->setAttribute('value',$block->dataValue('buttonText'));
            $region->setAttribute('value', $block->dataValue('region'));
            $template->setAttribute(('value'),$block->dataValue('template'));
            $classes->setAttribute('value',$block->dataValue('classes'));
            $itemCount->setAttribute('value',$block->dataValue('itemCount'));
        } else {
            $region->setAttribute('value','region:default');
            $template->setAttribute('value','common/block-layout/item-list');
        }

        $html = $view->formRow($sectionsMenu);
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($title);
        $html .= $view->formRow($introductionField);
        $html .= $view->formRow($buttonText);
        $html .= $view->formRow($buttonPath);
        $html .= $view->formRow($template);
        $html .= $view->formRow($itemCount);
        $html .= $view->formRow($classes);
        $html .= $view->formRow($region);
        $html .= '</div>';

        return $html;


    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headScript()->appendFile($view->assetUrl('js/regional_html_handler.js', 'AgileThemeTools'));
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block) {

        $data = $block->data();
        $pages = $this->sectionManager->getPagesBySection($data['section'],!empty($data['itemCount']) ? $data['itemCount'] : null);
        list($scope,$region) = explode(':',$data['region']);

        foreach ($pages as &$page) {
            $page['embeddedBlocks'] = [];
            foreach ($page['o:blocks-by-layout'] as $layout => $embeddedBlocks) {
                foreach ($embeddedBlocks as $id => $embeddedBlock) {
                    $blockView = clone $view;
                    $blockRepresentation = $embeddedBlock['o:blockRepresentation'];
                    // Not ideal. Doesn't fully simulate the build process as far as I can tell.
                    $render = $this->blockLayoutManager
                        ->get($layout)
                        ->render($blockView,$blockRepresentation);
                    $page['embeddedBlocks'][$layout][] = $render;
                }
            }
            $page['hasEmbeddedBlocks'] = count($page['embeddedBlocks']) > 0;
        }

        return $view->partial($data['template'], [
            'block' => $block,
            'pages' => $pages,
            'blockId' => $block->id(),
            'buttonText' => $data['buttonText'],
            'buttonPath' => $data['buttonPath'],
            'title' => $data['title'],
            'introduction' => $data['introduction'],
            'class' => $data['classes'],
            'hasPages' => count($pages) > 0,
            'hasTitle' => !empty($data['title']),
            'hasIntroduction' => !empty($data['introduction']),
            'hasButton' => !empty($data['buttonText']) && !empty($data['buttonPath']),
            'targetID' => '#' . $region
        ]);
    }


}


