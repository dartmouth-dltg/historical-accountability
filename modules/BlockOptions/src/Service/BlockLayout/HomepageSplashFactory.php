<?php
namespace BlockOptions\Service\BlockLayout;

use BlockOptions\Site\BlockLayout\HomepageSplash;
use BlockOptions\Site\BlockLayout\RegionalHtml;
use BlockOptions\Site\BlockLayout\Slideshow;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class HomepageSplashFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return HomepageSplash
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        return new HomepageSplash($htmlPurifier,$formElementManager);
    }
}
