<?php
namespace Workflow\Listener;

use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

/**
 * 
 * @author nmt
 *
 */
class WorkflowLogger implements EventSubscriberInterface
{
    /*  public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    } */

    public function onLeave(Event $event)
    {
        printf(
            'Blog post (id: "%s") performed transaction "%s" from "%s" to "%s"',
            $event->getSubject()->getId(),
            $event->getTransition()->getName(),
            implode(', ', array_keys($event->getMarking()->getPlaces())),
            implode(', ', $event->getTransition()->getTos())
        );
    }

    public static function getSubscribedEvents()
    {
        return array(
            'workflow.leave' => 'onLeave',
        );
    }
}