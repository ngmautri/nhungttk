<?php
namespace Workflow\Workflow\Procure\Listener;

use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\Event;
use Zend\Math\Rand;
use Application\Entity\NmtProcureWfWorkitem;
use Application\Entity\NmtWfToken;
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
        $enabledDate = new \DateTime();
        
        if (count($transtions) > 0) {
            
            // Get Workflow Entity
            $workflowFactory = get_class($wf->getWorkflowFactory());
            $workflowClass = get_class($wf);
            $workflowName = $event->getWorkflowName();
            
            // to handle the proxie object
            $className = $this->doctrineEM->getClassMetadata(get_class($event->getSubject()))
                ->getName();
            $subjectClass = $className;
            
            /*
             * printf('Factory: "%s" <br> Class: "%s" <br> Name: "%s"<br>subject: "%s" <br>',
             * $workflowFactory,
             * $workflowClass,
             * $workflowName,
             * $subjectClass);
             */
            
            // set agent_id, role_id
            $criteria = array(
                'workflowName' => $workflowName,
                'workflowFactory' => $workflowFactory,
                'workflowClass' => $workflowClass,
                'subjectClass' => $subjectClass,
                'isActive' => 1
            );
            
            $wf_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfWorkflow')->findOneBy($criteria);
            
            // current_workitem
            $criteria = array(
                'workflow' => $wf_entity,
                'transitionName' => $event->getTransition()->getName(),
                'subjectClass' => $subjectClass,
                'subjectId' => $event->getSubject()->getId()
            );
            
            /** @var \Application\Entity\NmtWfWorkitem $current_wi_entity*/
            $current_wi_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfWorkitem')->findOneBy($criteria);
            
            if ($current_wi_entity !== null) {
                $current_wi_entity->setFinishedDate(new \Datetime());
                $current_wi_entity->setWorkitemStatus("CONSUMED");
                $this->doctrineEM->persist($current_wi_entity);
            }
            
            foreach ($transtions as $t) {
                /** @var \Symfony\Component\Workflow\Transition $t*/
                
                /*
                 * printf('Enable : "%s" <br>',
                 * $t->getName()
                 * );
                 */
                
                // original place, consumed it
                $from_places = $t->getFroms();
                foreach ($from_places as $f_place) {
                    
                    $criteria = array(
                        'workflow' => $wf_entity,
                        'placeName' => $f_place
                    );
                    
                    /** @var \Application\Entity\NmtWfPlace $place*/
                    $place = $this->doctrineEM->getRepository('Application\Entity\NmtWfPlace')->findOneBy($criteria);
                    
                    // find tokens
                    $criteria = array(
                        'workflow' => $wf_entity,
                        'placeName' => $f_place,
                        'subjectClass' => $subjectClass,
                        'subjectId' => $event->getSubject()->getId()
                    );
                    /** @var \Application\Entity\NmtWfToken $place_token_entity*/
                    $place_token_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfToken')->findOneBy($criteria);
                    if ($place_token_entity !== null) {
                        $place_token_entity->setTokenStatus("CONSUMED");                        
                        $place_token_entity->setConsumedDate($enabledDate);
                        $this->doctrineEM->persist($place_token_entity);
                    }else{
                        $new_token = new NmtWfToken();
                        $new_token->setTokenStatus("CONSUMED");
                        $new_token->setWorkflow($wf_entity);
                        $new_token->setPlaceName($f_place);
                        $new_token->setPlace($place);
                        $new_token->setEnabledDate($enabledDate);
                        $new_token->setSubjectId($event->getSubject()
                            ->getId());
                        $new_token->setSubjectClass($subjectClass);
                        $new_token->setSubjectToken($event->getSubject()
                            ->getToken());
                        $this->doctrineEM->persist($new_token);
                        $this->doctrineEM->flush();
                    }
                }
                
                // destinition places $ and create token for place
                $to_places = $t->getTos();
                foreach ($to_places as $t_place) {
                    
                    // find place
                    $criteria = array(
                        'workflow' => $wf_entity,
                        'placeName' => $t_place
                    );
                    
                    /** @var \Application\Entity\NmtWfPlace $place*/
                    $place = $this->doctrineEM->getRepository('Application\Entity\NmtWfPlace')->findOneBy($criteria);
                    if ($place == null) {
                        // break;
                    }
                    
                    // find token
                    $criteria = array(
                        'workflow' => $wf_entity,
                        'placeName' => $t_place,
                        'subjectClass' => $subjectClass,
                        'subjectId' => $event->getSubject()->getId()
                    );
                    
                    /** @var \Application\Entity\NmtWfToken $place_token_entity*/
                    $place_token_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfToken')->findOneBy($criteria);
                    
                    if ($place_token_entity == null) {
                        $new_token = new NmtWfToken();
                        $new_token->setTokenStatus("ENABLED");                        
                        $new_token->setWorkflow($wf_entity);
                        $new_token->setPlaceName($t_place);
                        $new_token->setPlace($place);
                        $new_token->setEnabledDate($enabledDate);
                        $new_token->setSubjectId($event->getSubject()
                            ->getId());
                        $new_token->setSubjectClass($subjectClass);
                        $new_token->setSubjectToken($event->getSubject()
                            ->getToken());
                        $this->doctrineEM->persist($new_token);
                        $this->doctrineEM->flush();
                    } else {
                        $place_token_entity->setEnabledDate($enabledDate);
                        $this->doctrineEM->persist($place_token_entity);
                    }
                }
                
                $criteria = array(
                    'workflow' => $wf_entity,
                    'transitionName' => $t->getName(),
                    'isActive' => 1
                );
                
                /** @var \Application\Entity\NmtWfTransition $t_entity*/
                $t_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfTransition')->findOneBy($criteria);
                
                if ($t_entity !== null) {
                    
                    // set agent_id, role_id
                    $agents = $this->doctrineEM->getRepository('Application\Entity\NmtWfTransitionAgent')->findBy(array(
                        "transition" => $t_entity->getId()
                    ));
                    
                    if (count($agents) > 0) {
                        
                        foreach ($agents as $a) {
                            /** @var \Application\Entity\NmtWfTransitionAgent $a*/
                            $entity = new NmtWfWorkitem();
                            $entity->setAgent($a->getAgent());
                            $entity->setWorkflow($wf_entity);
                            $entity->setWorkflowName($event->getWorkflowName());
                            $entity->setWorkitemStatus("ENABLED");
                            $entity->setTransitionName($t->getName());
                            $entity->setTransition($t_entity);
                            $entity->setEnabledDate($enabledDate);
                            $entity->setToken(Rand::getString(10, parent::CHAR_LIST, true) . "_" . Rand::getString(21, parent::CHAR_LIST, true));
                            $entity->setSubjectId($event->getSubject()
                                ->getId());
                            $entity->setSubjectClass(get_class($event->getSubject()));
                            $entity->setRemarks("Workitem: please do '" . $t_entity->getTransitionName() . "' on '" . $subjectClass . " ID (" . $event->getSubject()
                                ->getId() . ")'");
                            $this->doctrineEM->persist($entity);
                        }
                    }
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