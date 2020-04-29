<?php
namespace BlockOptions\Service\BlockLayout;

use BlockOptions\Site\BlockLayout\ProfileBiography;
use BlockOptions\Site\BlockLayout\RegionalHtml;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProfileBiographyFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return ProfileBiography
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        return new ProfileBiography($htmlPurifier,$formElementManager);
    }
}
