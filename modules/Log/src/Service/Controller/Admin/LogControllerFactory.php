<?php
namespace Log\Service\Controller\Admin;

use Interop\Container\ContainerInterface;
use Log\Controller\Admin\LogController;
use Zend\ServiceManager\Factory\FactoryInterface;

class LogControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        return new LogController(
            (bool) $serviceLocator->get('Config')['logger']['writers']['db']
        );
    }
}
