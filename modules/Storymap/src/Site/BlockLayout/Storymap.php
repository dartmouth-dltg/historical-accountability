<?php

namespace Storymap\Site\BlockLayout;

use Omeka\Api\Manager as ApiManager;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Entity\SitePageBlock;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Stdlib\ErrorStore;
use Storymap\Form\StorymapBlockForm;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\View\Renderer\PhpRenderer;

class Storymap extends AbstractBlockLayout {

  /**
   * @var ApiManager
   */
  protected $apiManager;

  /**
   * @var FormElementManager
   */
  protected $formElementManager;

  /**
   * @var bool
   */
  protected $useExternal;

  /**
   * @param ApiManager $apiManager
   * @param FormElementManager $formElementManager
   * @param bool $useExternal
   */
  public function __construct(ApiManager $apiManager, FormElementManager $formElementManager) {
    $this->apiManager = $apiManager;
    $this->formElementManager = $formElementManager;
  }

  public function getLabel() {
    return 'Storymap'; // @translate
  }

  public function prepareForm(PhpRenderer $view) {
    $view->headLink()
      ->prependStylesheet($view->assetUrl('css/advanced-search.css', 'Omeka'));
    $view->headScript()
      ->appendFile($view->assetUrl('js/storymap-item-pool.js', 'Storymap'));
  }

  public function form(PhpRenderer $view, SiteRepresentation $site,
                       SitePageRepresentation $page = NULL, SitePageBlockRepresentation $block = NULL
  ) {
    $data = $block ? $block->data() : [];
    $attachments = $view->blockAttachmentsForm($block);
    $form = $this->formElementManager->get(StorymapBlockForm::class);
    $form->init();

    $addedBlock = empty($data);
    if ($addedBlock) {
      $data['args'] = $view->setting('storymap_defaults');
      $data['item_pool'] = $site->itemPool();
      $itemCount = NULL;
    }
    else {
      $itemCount = $this->itemCount($data);
    }

    $form->setData([
      'o:block[__blockIndex__][o:data][args]' => $data['args'],
      'o:block[__blockIndex__][o:data][item_pool]' => $data['item_pool'],
    ]);

    return $view->partial(
      'common/block-layout/storymap-form',
      [
        'form' => $form,
        'data' => $data,
        'attachments' => $attachments,
        'itemCount' => $itemCount,
      ]
    );

    return $view->blockStorymapForm($block);
  }

  public function prepareRender(PhpRenderer $view) {

    $view->headLink()
      ->appendStylesheet('//cdn.knightlab.com/libs/storymapjs/latest/css/storymap.css');
    $view->headScript()
      ->appendFile('//cdn.knightlab.com/libs/storymapjs/latest/js/storymap.js');

  }

  public function render(PhpRenderer $view, SitePageBlockRepresentation $block) {
    $library = $view->setting('storymap_library');
    return $view->partial(
      'common/block-layout/storymap_knightlab',
      [
        'blockId' => $block->id(),
        'data' => $block->data(),
      ]
    );
  }

  public function onHydrate(SitePageBlock $block, ErrorStore $errorStore) {
    $data = $block->getData();

    // Set some default values in case of error.
    $data += [
      'item_pool' => [],
      'args' => [
        'item_date' => 'dcterms:date',
        'viewer' => '{}',
      ],
    ];

    $data['args']['viewer'] = trim($data['args']['viewer']);
    if ($data['args']['viewer'] === '') {
      $data['args']['viewer'] = '{}';
    }


    $block->setData($data);
  }

  /**
   * Helper to get the item count for the item pool, filtered of empty dates.
   *
   * @param array $data
   *
   * @return int
   */
  protected function itemCount($data) {
    $params = $data['item_pool'];
    // Add the param for the date: return only if not empty.
    $params['property'][] = [
      'joiner' => 'and',
      'type' => 'ex',
    ];
    $params['limit'] = 0;
    $itemCount = $this->apiManager->search('items', $params)->getTotalResults();
    return $itemCount;
  }
}
