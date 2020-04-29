<?php

namespace CkEditorLocal;

use Omeka\Module\AbstractModule;
use Omeka\Form\SitePageForm;
use HuroniaBlockOptions\Form\RegionalOptionsForm;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
    const NAMESPACE = __NAMESPACE__;

    public function getConfig()

    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

    }
}