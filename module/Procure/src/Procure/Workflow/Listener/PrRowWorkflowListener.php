<?php
namespace Procure\Workflow\Listener;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

/**
 *
 * @author nmt
 *        
 */
class PrRowWorkflowListener implements EventSubscriberInterface
{

    protected $doctrineEM;

    protected $workflow;

    /**
     *
     * @param EntityManager $doctrineEM
     */
    public function __construct(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
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
        // var_dump ($title);
        // var_dump ($event->getName());
        
        if (empty($title)) {
            // Posts with no title should not be allowed
            $event->setBlocked(true);
        }
    }

    /**
     * need to get enable transitions
     * 
     * @param Event $event
     */
    public function onSubmitPR(Event $event)
    {   
        // enable transition;
        $transtions = $this->workflow->getEnabledTransitions($event->getSubject());
        
        if (count($transtions) > 0)
            
            /** @todo Create Workitem */
            foreach ($transtions as $t) {
                echo "To created workitem for: " . $t->getName() . "\n";
            }
        else {
            echo "END...";
        }
        
        printf(
            'Blog post (id: "%s") performed transaction "%s" from "%s" to "%s"',
            $event->getSubject()->getId(),
            $event->getTransition()->getName(),
            implode(', ', array_keys($event->getMarking()->getPlaces())),
            implode(', ', $event->getTransition()->getTos())
            );
        
        //doctrine update.
        //echo("current:".$event->getSubject()->getCurrentState());
        $this->doctrineEM->flush($event->getSubject());
     }

    /**
     *
     * @return string[][]
     */
    public static function getSubscribedEvents()
    {
        return array(
            'workflow.PR_WORKFLOW.guard.to_review' => array(
                'guardReview'
            ),
            'workflow.PR_WORKFLOW.entered' => array(
                'onSubmitPR'
            )
        );
    }

    /**
     *
     * @return the $workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     *
     * @param field_type $workflow
     */
    public function setWorkflow($workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     *
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param field_type $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}