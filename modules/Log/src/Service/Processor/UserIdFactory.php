<?php
namespace Log\Service\Processor;

use Interop\Container\ContainerInterface;
use Log\Processor\UserId;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserIdFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $user = $services->get('Omeka\AuthenticationService')->getIdentity();
        return new UserId($user);
    }
}
