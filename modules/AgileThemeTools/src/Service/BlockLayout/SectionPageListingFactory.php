<?php

namespace AgileThemeTools\Service\BlockLayout;

use AgileThemeTools\Controller\SectionManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use AgileThemeTools\Site\BlockLayout\SectionPageListing;
use Omeka\Stdlib\ErrorStore;

class SectionPageListingFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return SectionPageListing
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        $sectionManager = $serviceLocator->get('SectionManager');
        $blockLayoutManager = $serviceLocator->get('Omeka\BlockLayoutManager');
        $errorStore = new ErrorStore();
        /* $siteSlug = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch()->getParam('site-slug');
        $api = $serviceLocator->get('Omeka\ApiManager');
        $site = $api->read('sites', ['slug' => $siteSlug])->getContent();

        foreach($site->publicNav()->toArray() as $item) {
            foreach($item as $key => $item) {
                if (is_string($item)) {
                    var_dump(strtoupper($key));
                    var_dump($item);
                }
            }
        }*/

       // var_dump($site->publicNav()->toArray());
        return new SectionPageListing($sectionManager, $blockLayoutManager, $htmlPurifier, $formElementManager,$errorStore);
    }
}
