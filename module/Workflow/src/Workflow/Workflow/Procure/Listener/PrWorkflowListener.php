<?php
namespace Workflow\Workflow\Procure\Listener;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;
use Zend\Math\Rand;
use Application\Entity\NmtProcureWfWorkitem;

/**
 *
 * @author nmt
 *        
 */
class PrWorkflowListener implements EventSubscriberInterface
{
    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
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
        $this->doctrineEM->persist($event->getSubject());
         
        if (count($transtions) > 0)
            
            /** @todo Create Workitem */
            foreach ($transtions as $t) {
                
                /** @var \Symfony\Component\Workflow\Transition $t*/                
                $entity =  new NmtProcureWfWorkitem ();
                $entity->setWorkflowName($event->getWorkflowName());
                $entity->setPr($event->getSubject());
                $entity->setWorkitemStatus("ENABLED");
                $entity->setTransitionName($t->getName());
                $entity->setEnabledDate(new \DateTime());
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                $entity->setSubjectId($event->getSubject()->getId());
                $entity->setSubjectClass(get_class($event->getSubject()));
                $this->doctrineEM->persist($entity);
                
                //$this->doctrineEM->flush();
                //echo "To created workitem for: " . $t->getName() . "\n";
            }
        else {
            echo "END...";
        }
        
        printf(
            'WF post (id: "%s") performed transaction "%s" from "%s" to "%s"',
            $event->getSubject()->getId(),
            $event->getTransition()->getName(),
            implode(', ', array_keys($event->getMarking()->getPlaces())),
            implode(', ', $event->getTransition()->getTos())
            );
        
        //doctrine update.
        //echo("current:".$event->getSubject()->getCurrentState());
        $this->doctrineEM->flush();
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