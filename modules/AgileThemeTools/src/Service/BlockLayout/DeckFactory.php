<?php
namespace AgileThemeTools\Service\BlockLayout;

use AgileThemeTools\Site\BlockLayout\Deck;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DeckFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return Deck
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        return new Deck($htmlPurifier,$formElementManager);
    }
}
