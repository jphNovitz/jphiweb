<?php

namespace App\EventSubscriber;

use App\Entity\Blog;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TextStyleEasyAdminSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['addCustomClassesToContentBeforePersist'],
            BeforeEntityUpdatedEvent::class => ['addCustomClassesToContentBeforeUpdate'],
        ];
    }

    public function addCustomClassesToContentBeforePersist(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Blog)) {
            return;
        }

//        $content = preg_replace('/<ol>/', '<ol class="my-custom-class">', $entity->getContent());
        $entity->setContent($this->addCustomClasses($entity->getContent()));
    }
    public function addCustomClassesToContentBeforeUpdate(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Blog)) {
            return;
        }

        $entity->setContent($this->addCustomClasses($entity->getContent()));
    }

    public function addCustomClasses($content){
        $content = preg_replace('/<ol>/', '<ol class="ol-numeric">', $content);
        return $content ;

    }
}
