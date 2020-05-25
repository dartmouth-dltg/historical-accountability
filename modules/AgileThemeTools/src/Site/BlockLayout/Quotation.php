<?php
namespace AgileThemeTools\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Text;
use Zend\View\Renderer\PhpRenderer;

class Quotation extends AbstractBlockLayout
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
        return 'Quotation'; // @translate
    }


    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $html = isset($data['quotation']) ? $this->htmlPurifier->purify($data['quotation']) : '';
        $data['quotation'] = $html;
        $block->setData($data);
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $textarea = new Textarea("o:block[__blockIndex__][o:data][quotation]");
        $textarea->setAttribute('class', 'block-html full wysiwyg');
        $textarea->setAttribute('rows',20);
        $textarea->setLabel('Quotation');


        $attribution = new Text("o:block[__blockIndex__][o:data][attribution]");
        $attribution->setLabel('Attribution');

        if ($block) {
            $textarea->setAttribute('value', $block->dataValue('quotation'));
            $attribution->setAttribute('value', $block->dataValue('attribution'));
        }

        $html = '';
        $html .= $view->formRow($textarea);
        $html .= $view->formRow($attribution);
        return $html;


    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block) {

        $data = $block->data();

        return $view->partial(
            'common/block-layout/quotation.phtml',
            [
                'html' => $data['quotation'],
                'attribution' => $data['attribution'],
                'blockId' => $block->id(),
            ]
        );
    }


}


