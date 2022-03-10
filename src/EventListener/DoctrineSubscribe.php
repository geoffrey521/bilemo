<?php

namespace App\EventListener;

use App\Model\EntityTimestampableInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class DoctrineSubscribe implements EventSubscriberInterface
{

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $object = $eventArgs->getObject();
        if ($object instanceof EntityTimestampableInterface) {
            $object->setCreatedAt();
            $object->setUpdatedAt();
        }
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $object = $eventArgs->getObject();
        if ($object instanceof EntityTimestampableInterface) {
            $object->setUpdatedAt();
        }
    }
}
