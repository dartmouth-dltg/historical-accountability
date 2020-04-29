<?php
namespace BlockOptions\Service\Form\Element;

use BlockOptions\Form\Element\SectionsMenuSelect;
use Omeka\Form\Element\ResourceSelect;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use BlockOptions\Controller\SectionManager;

class SectionsMenuSelectFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $element = new SectionsMenuSelect();
        $element->setSectionManager($services->get('SectionManager'));
        return $element;
    }
}
