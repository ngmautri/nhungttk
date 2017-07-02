<?php
namespace Workflow\Listener;

use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Registry;

/**
 * 
 * @author nmt
 *
 */
class PrWorkflowistener implements EventSubscriberInterface{

    private $register;
    
    public function __construct(Registry $register)
    {
        $this->register = $register;
    }

    /**
     * 
     * @param GuardEvent $event
     */
    public function guardReview(GuardEvent $event)
    {
        /** @var \Application\Entity\NmtProcurementPr $post */
        $post = $event->getSubject();
        $title = $post->getPrNumber();
          
        if (empty($title)) {
             $event->setBlocked(true);
        }
    }
    
    /**
     * need to get enable transitions
     * @param Event $event
     */
    public function onPlaceEntered(Event $event)
    {
        $transtions = $this->register->get($event->getSubject())->getEnabledTransitions($event->getSubject());
        if(count($transtions)>0)
        foreach($transtions as $t){
            echo "to created workitem for: " . $t->getName() ."\n";
        }else{
            echo "END...";
        }
        printf(
            'WF: "%s": PR (id: "%s") performed transaction "%s" from "%s" to "%s"',
            $event->getWorkflowName(),
            $event->getSubject()->getId(),
            $event->getTransition()->getName(),
            implode(', ', array_keys($event->getMarking()->getPlaces())),
            implode(', ', $event->getTransition()->getTos())
            );
    }
    
    /**
     * 
     * @return string[][]
     */
    public static function getSubscribedEvents()
    {
        return array(
            'workflow.PR_WORKFLOW.guard' => array('guardReview'),
            'workflow.PR_WORKFLOW.entered' => array('onPlaceEntered')
        );
    }
}