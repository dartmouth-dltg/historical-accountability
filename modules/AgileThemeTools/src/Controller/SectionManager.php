<?php

namespace AgileThemeTools\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Omeka\Api\Manager;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\Filter\RealPath;

class SectionManager extends AbstractActionController {

    protected $api;
    protected $htmlPurifier;
    protected $formElementManager;
    protected $siteSlug;
    protected $site;
    protected $pages;
    protected $template_path;
    protected $pageIndex;

    function __construct(Manager $api, HtmlPurifier $htmlPurifier,FormElementManager $formElementManager, $siteSlug, SiteRepresentation $site) {
        $this->api = $api;
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
        $this->siteSlug = $siteSlug;
        $this->site = $site;
        $this->pages = [];
        $this->pageIndex = [];
        $this->indexPages();

        $filter = new RealPath();
        $this->template_path = $filter(__DIR__ .  '/../../view');
    }

    function indexAction()
    {
       return $this->getSections();

    }

    /*
     * @method indexPages()
     *
     * Populates an array of pages based on the public navigation structure.
     * Indexes subpages by parent slug for easy retrieval of child pages.
     */

    private function indexPages() {
        // See toArray() https://docs.zendframework.com/zend-navigation/containers/
        // Also, Omeka keeps its site and page slugs as $page['params']['site-slug'] and $page['params']['page-slug']
        // Items without a page-slug are likely direct routes and ignored as sections.

        $this->pages = $this->site->publicNav()->toArray();
        $this->getSections(); // Populates the page index. Requires pages be built.
    }

    /*
     * @method getSections()
     * This function does double-duty (for convenience – probably should refactor).
     * - It returns a full list of pages and nested subpages that can be designated a “section” of the site. This
     *   will be used to populate the section listing menu.
     * - It populates the pageIndex property, which allows functions to look up all the immediate child pages by slug.
     *
     */

    public function getSections() {

        $sections = [];
        foreach ($this->pages as $page) {
            // Top-level user-added pages are considered a “section”.
            //var_dump(array_keys($page));
            if (array_key_exists('params',$page) && array_key_exists('page-slug',$page['params'])) {
                $sections[$page['params']['page-slug']] = $page['label'];
                $this->pageIndex[$page['params']['page-slug']] = $page['pages'];
                $this->extractSubPages($page,$sections);
            }
        }

        return $sections;

    }

    /*
     * @method extractSubPages
     * A recursive function that traverses child pages in the navigation tree.
     */

    private function extractSubPages($page,&$sections,$depth=1) {
        if (array_key_exists('pages',$page) && count($page['pages']) > 0) {
            foreach($page['pages'] as $subpage) {
                if (array_key_exists('params',$subpage) && array_key_exists('page-slug',$subpage['params'])) {
                    $sections[$subpage['params']['page-slug']] = str_pad('',$depth, "  ",STR_PAD_LEFT) . str_pad($subpage['label'],strlen($subpage['label']) + $depth,"-",STR_PAD_LEFT);
                    $this->extractSubPages($subpage,$sections,$depth+1);
                    $this->pageIndex[$subpage['params']['page-slug']] = $subpage['pages'];
                }
            }
        }
    }

    /*
     * @method getPagesBySection
     * Identifies and returns an array of page representation objects suitable for rendering. Method is provided
     * a page slug representing a section and returns child pages if they exist.
     */

    public function getPagesBySection($sectionSlug,$count=null){
        $sectionPages = [];

        // Check to see if section has indexed pages, otherwise return an empty array.
        if (!key_exists($sectionSlug,$this->pageIndex)) {
            return [];
        }

        foreach($this->pageIndex[$sectionSlug] as $childPage) {
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

                    // Ensure that a layout is set and that the Section Listing blocks are removed to prevent recursion.

                    $blockLayout = empty($serializedBlockInfo['o:layout']) || $serializedBlockInfo['o:layout'] == 'sectionPageListing' ? null : $serializedBlockInfo['o:layout'];

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