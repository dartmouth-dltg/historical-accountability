<?php
namespace BlockOptions\Service\BlockLayout;

use BlockOptions\Site\BlockLayout\ProfilePicture;
use BlockOptions\Site\BlockLayout\RegionalHtml;
use BlockOptions\Site\BlockLayout\RepresentativeImage;
use BlockOptions\Site\BlockLayout\Slideshow;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RepresentativeImageFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return RepresentativeImage
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        return new RepresentativeImage($htmlPurifier,$formElementManager);
    }
}
