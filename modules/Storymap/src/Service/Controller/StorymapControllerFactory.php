<?php
namespace Storymap\Service\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Storymap\Controller\StorymapController;

class StorymapControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedNamed, array $options = null)
    {
        return new StorymapController(
            $services->get('Omeka\EntityManager')
        );
    }
}
