<?php
namespace Log\Service\Job\DispatchStrategy;

use Log\Job\DispatchStrategy\Synchronous;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class SynchronousFactory implements FactoryInterface
{
    /**
     * Create the PhpCli strategy service.
     *
     * @return Synchronous
     */
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new Synchronous($services);
    }
}
