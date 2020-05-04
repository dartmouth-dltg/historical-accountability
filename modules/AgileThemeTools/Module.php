<?php

namespace AgileThemeTools;

use Doctrine\ORM\Events;
use AltText\Db\Event\Listener\DetachOrphanMappings;
use AltText\Entity\AltText as AltTextEntity;
use Omeka\Api\Representation\MediaRepresentation;
use Omeka\Entity\Media as MediaEntity;
use Omeka\Module\AbstractModule;
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
        // $acl = $this->getServiceLocator()->get('Omeka\Acl');
        //$acl->allow(null, [\AgileThemeTools\Controller\BlockOptionsAdminController::class]);
        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $em->getEventManager()->addEventListener(
            Events::preFlush,
            new DetachOrphanMappings
        );
        

    }
    
    
    // Code borrowed from the AltText module (with thanks!). Substitutes dc:description if no Alt tag provided.
    // Defaults to the title of the file, which is often just the filename.
    
    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            '*',
            'view_helper.thumbnail.attribs',
            function (Event $event) {
                $media = $event->getParam('primaryMedia');
                if (!$media) {
                    return;
                }

                $attribs = $event->getParam('attribs');
                
                if (empty($attribs['alt'])) {
                  $item = $media->item();
                  $description = $item->value('dcterms:description');
                  $attribs['alt'] = !empty($description) ? htmlspecialchars(strip_tags($description)) : $media->displayTitle();
                }

                $event->setParam('attribs', $attribs);
            }
        );
      }

}