<?php
namespace AltText\Db\Event\Listener;

use Doctrine\ORM\Event\PreFlushEventArgs;
use AltText\Entity\AltText;

/**
 * Automatically detach alt texts that reference unknown items.
 */
class DetachOrphanMappings
{
    /**
     * Detach all AltText entities that reference media not currently in the
     * entity manager.
     *
     * @param PreFlushEventArgs $event
     */
    public function preFlush(PreFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();
        $identityMap = $uow->getIdentityMap();

        if (isset($identityMap[AltText::class])) {
            foreach ($identityMap[AltText::class] as $altText) {
                if (!$em->contains($altText->getMedia())) {
                    $em->detach($altText);
                }
            }
        }
    }
}
