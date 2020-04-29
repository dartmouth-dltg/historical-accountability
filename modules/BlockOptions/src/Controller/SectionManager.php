<?php

namespace BlockOptions\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Omeka\Api\Manager;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\View\Renderer\PhpRenderer;
use Zend\Filter\RealPath;

class SectionManager extends AbstractActionController {

    protected $api;
    protected $htmlPurifier;
    protected $formElementManager;
    protected $siteSlug;
    protected $site;
    protected $pages;
    protected $template_path;

    function __construct(Manager $api, HtmlPurifier $htmlPurifier,FormElementManager $formElementManager, $siteSlug, SiteRepresentation $site) {
        $this->api = $api;
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
        $this->siteSlug = $siteSlug;
        $this->site = $site;

        // See toArray() https://docs.zendframework.com/zend-navigation/containers/
        // Also, Omeka keeps its site and page slugs as $page['params']['site-slug'] and $page['params']['page-slug']
        // Items without a page-slug are likely direct routes and ignored as sections.
        $this->pages = $this->site->publicNav()->toArray();

        $filter = new RealPath();
        $this->template_path = $filter(__DIR__ .  '/../../view');
    }

    function indexAction()
    {
       return $this->getSections();

    }

    public function getSections() {

        $sections = [];
        foreach ($this->pages as $page) {
            // Top-level user-added pages are considered a “section”.
            if (array_key_exists('params',$page) && array_key_exists('page-slug',$page['params'])) {
                $sections[$page['params']['page-slug']] = $page['label'];
            }
        }

        asort($sections);
        return $sections;

    }

    public function getPagesBySection($sectionSlug,$count=null){
        $view = new PhpRenderer();
        $sectionPages = [];
        foreach($this->pages as $page) {
            if (array_key_exists('params',$page) && array_key_exists('page-slug',$page['params']) && $page['params']['page-slug'] == $sectionSlug && array_key_exists('pages',$page) && count($page['pages']) > 0) {
                foreach($page['pages'] as $childPage) {
                    if (array_key_exists('params',$childPage) && array_key_exists('page-slug',$childPage['params'])) {

                        $childPageRepresentation = $this->api->read('site_pages',[
                            'slug' => $childPage['params']['page-slug'],
                            'site' => $this->site->id()
                        ])->getContent();

                        $childPageData = $childPageRepresentation->getJsonLd();
                        $childPageData['o:url'] = $childPageRepresentation->siteUrl();

                        // Block Objects

                        $childPageData['o:blocks'] = [];
                        $childPageData['o:blocks-by-layout'] = [];

                        foreach($childPageRepresentation->blocks() as $block) {
                            $block->page_title = $childPageData['o:title'];
                            $block->page_url = $childPageData['o:url'];

                            $serializedBlockInfo = $block->jsonSerialize();
                            $serializedBlockInfo['page_title'] = $childPageData['o:title'];
                            $serializedBlockInfo['page_url'] = $childPageData['o:url'];
                            
                            $serializedBlockInfo['o:blockRepresentation'] = $block;
                            $childPageData['o:blocks'][$block->id()] = $serializedBlockInfo;

                            $blockLayout = !empty($serializedBlockInfo['o:layout']) ? $serializedBlockInfo['o:layout'] : null;

                            if ($blockLayout) {
                                if (!array_key_exists($blockLayout,$childPageData['o:blocks-by-layout'])) {
                                    $childPageData['o:blocks-by-layout'][$blockLayout][$block->id()] = $serializedBlockInfo;
                                }
                            }
                        }

                        $childPageData['o:pageRepresentation'] = $childPageRepresentation;
                        $sectionPages[$childPage['params']['page-slug']] = $childPageData;
                    }
                }
            }
        }

        if ($count && count($sectionPages) > 0) {
            $randomized = [];
            $randIndex = array_rand($sectionPages,$count <= count($sectionPages) ? $count : count($sectionPages));

            foreach ((array)$randIndex as $key) {
                $randomized[$key] = $sectionPages[$key];
            }

            $sectionPages = $randomized;

        }

        return $sectionPages;

    }
}