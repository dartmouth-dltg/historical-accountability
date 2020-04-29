<?php
namespace BlockOptions\Service\BlockLayout;

use BlockOptions\Site\BlockLayout\ProfilePicture;
use BlockOptions\Site\BlockLayout\RegionalHtml;
use BlockOptions\Site\BlockLayout\Slideshow;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProfilePictureFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return ProfilePicture
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        return new ProfilePicture($htmlPurifier,$formElementManager);
    }
}
