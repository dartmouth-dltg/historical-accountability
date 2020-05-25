<?php
namespace AgileThemeTools\Service\BlockLayout;

use AgileThemeTools\Site\BlockLayout\ItemListing;
use Interop\Container\ContainerInterface;
use Omeka\Stdlib\ErrorStore;
use Zend\ServiceManager\Factory\FactoryInterface;

class ItemListingFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return ItemListing
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        $errorStore = new ErrorStore();
        $blockLayoutManager = $serviceLocator->get('Omeka\BlockLayoutManager');


        return new ItemListing($blockLayoutManager,$htmlPurifier,$formElementManager,$errorStore);
    }
}
