<?php
namespace Workflow\Workflow\Procure\Listener;

use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\Event;
use Zend\Math\Rand;
use Application\Entity\NmtProcureWfWorkitem;
use Workflow\Workflow\AbstractWorkflowListener;
use Application\Entity\NmtWfWorkitem;

/**
 *
 * @author nmt
 *        
 */
class PrWorkflowListener extends AbstractWorkflowListener
{

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
        /** @var \Workflow\Workflow\AbstractWorkflow $wf*/
        $wf = $this->getWorkflow();
        
        // Marking with new status
        $this->doctrineEM->persist($event->getSubject());
        
        /** @var \Symfony\Component\Workflow\Workflow $wf_instance*/
        $wf_instance = $wf->getWorkflowInstance();
        $transtions = $wf_instance->getEnabledTransitions($event->getSubject());
        
        if (count($transtions) > 0) {
            
            // Get Workflow Entity
            $workflowFactory = get_class($wf->getWorkflowFactory());
            $workflowClass = get_class($wf);
            $workflowName = $event->getWorkflowName();
            $subjectClass = get_class($event->getSubject());
            
            // set agent_id, role_id
            $criteria = array(
                'workflowName' => $workflowName,
                'workflowFactory' => $workflowFactory,
                'workflowClass' => $workflowClass,
                'subjectClass' => $subjectClass,
                'isActive' => 1
            );
            
            $wf_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfWorkflow')->findOneBy($criteria);
            
            // current_transtion
            $criteria = array(
                'workflow' => $wf_entity,
                'transitionName' => $event->getTransition()->getName(),
                'subjectClass'=> $subjectClass,
                'subjectId'=> $event->getSubject()->getId(),
              );
            
             /** @var \Application\Entity\NmtWfWorkitem $current_wi_entity*/
            $current_wi_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfWorkitem')->findOneBy($criteria);
            if($current_wi_entity!==null){
                $current_wi_entity->setFinishedDate(new \Datetime());
                $current_wi_entity->setWorkitemStatus("CONSUMED");
                $this->doctrineEM->persist($current_wi_entity);
            }
            
            
            
            /**
             * set agent_id, role_id
             * @todo Create Workitem
             */
            foreach ($transtions as $t) {
                /** @var \Symfony\Component\Workflow\Transition $t*/
                
                // set agent_id, role_id
                $criteria = array(
                    'workflow' => $wf_entity,
                    'transitionName' => $t->getName(),
                    'isActive' => 1
                );
                
                /** @var \Application\Entity\NmtWfTransition $t_entity*/
                $t_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfTransition')->findOneBy($criteria);
                
                if ($t_entity !== null) {
                    $entity = new NmtWfWorkitem();
                    $entity->setWorkflow($wf_entity);
                    $entity->setWorkflowName($event->getWorkflowName());
                    $entity->setWorkitemStatus("ENABLED");
                    $entity->setTransitionName($t->getName());
                    $entity->setTransition($t_entity);
                    $entity->setEnabledDate(new \DateTime());
                    $entity->setToken(Rand::getString(10, parent::CHAR_LIST, true) . "_" . Rand::getString(21, parent::CHAR_LIST, true));
                    $entity->setSubjectId($event->getSubject()
                        ->getId());
                    $entity->setSubjectClass(get_class($event->getSubject()));
                    $entity->setRemarks("Workitem: please do '". $t_entity->getTransitionName() . "' on '" . $subjectClass. " ID (".  $event->getSubject()->getId() .")'");
                    $this->doctrineEM->persist($entity);
                }
            }
        } else {
            echo "END...";
        }
        
        printf('WF post (id: "%s") performed transaction "%s" from "%s" to "%s"', $event->getSubject()->getId(), $event->getTransition()->getName(), implode(', ', array_keys($event->getMarking()->getPlaces())), implode(', ', $event->getTransition()->getTos()));
        
        // doctrine update.
        // echo("current:".$event->getSubject()->getCurrentState());
        $this->doctrineEM->flush();
    }

    /**
     *
     * @return string[][]
     */
    public static function getSubscribedEvents()
    {
        return array(
            'workflow.PR_SUBMIT.guard.to_review' => array(
                'guardReview'
            ),
            'workflow.PR_SUBMIT.entered' => array(
                'onSubmitPR'
            )
        );
    }
}