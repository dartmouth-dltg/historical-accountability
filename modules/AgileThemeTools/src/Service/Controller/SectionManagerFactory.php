<?php

namespace AgileThemeTools\Service\Controller;

use AgileThemeTools\Controller\SectionManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\EventManager\EventManager;
use Omeka\Api\Representation\SiteRepresentation;

class SectionManagerFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return SectionManager
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        $api = $serviceLocator->get('Omeka\ApiManager');
        $siteSlug = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch()->getParam('site-slug');
        $site = $api->read('sites', ['slug' => $siteSlug])->getContent();
        return new SectionManager($api, $htmlPurifier, $formElementManager,$siteSlug,$site);
    }
}
