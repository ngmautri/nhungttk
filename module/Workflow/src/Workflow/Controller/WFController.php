<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Workflow\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Workflow\Service\WorkflowService;
use Application\Entity\NmtProcurePr;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Application\Entity\NmtWfWorkflow;
use Zend\Math\Rand;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtWfTransition;
use Application\Entity\NmtWfPlace;

/*
 * Control Panel Controller
 */
class WFController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;
    protected $wfService;

    /**
     *
     * @return the $wfService
     */
    public function getWfService()
    {
        return $this->wfService;
    }

    /**
     *
     * @param field_type $wfService
     */
    public function setWfService(WorkflowService $wfService)
    {
        $this->wfService = $wfService;
    }

    /*
     * Defaul Action
     */
    public function indexAction()
    {
        $em = $this->doctrineEM;
        $data = $em->getRepository('Application\Entity\NmtWfWorkflow')->findAll();
        foreach ($data as $row) {
            echo $row->getWorkflowName();
            echo '<br />';
        }
    }

    /*
     * Defaul Action
     */
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $em = $this->doctrineEM;
            $input = new \Application\Entity\NmtWfWorkflow();
            $input->setWorkflowName($request->getPost('workflow_name'));
            $input->setWorkflowDescription($request->getPost('workflow_description'));
            
            $u = $this->doctrineEM->find('Application\Entity\MlaUsers', 39);
            $input->setWorkflowCreatedBy($u);
            $input->setWorkflowCreatedOn(new \DateTime());
            
            $em->persist($input);
            $em->flush();
        }
        
        // $this->redirect ()->toUrl ( 'home' );
    }

    /*
     * Defaul Action
     */
    public function listAction()
    {
        $list = array();
        
        $subjects = $this->wfService->getSupportedSubjects();
        foreach ($subjects as $s) {
            
            /** @var \Workflow\Workflow\WorkflowFactoryInterface $wf_factory */
            $wf_factory = $this->wfService->getWorkFlowFactory($s);
            
            if (! $wf_factory == null) {
                /** @var \Workflow\Workflow\AbstractWorkflow  $wf */
                $wf_list = $wf_factory->getWorkFlowList();
                
                foreach ($wf_list as $k => $v) {
                    
                    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                        "email" => $this->identity()
                    ));
                    // echo get_class($wf->getWorkflowFactory());
                    // echo get_class($wf->getWorkflowName());
                    
                    /** @var \Workflow\Workflow\AbstractWorkflow  $v */
                    
                    $workflowName = $v->getWorkflowName();
                    $workflowFactory = get_class($v->getWorkflowFactory());
                    $workflowClass = get_class($v);
                    $subjectClass = get_class($v->getSubject());
                    $criteria = array(
                        'workflowName' => $workflowName,
                        'workflowFactory' => $workflowFactory,
                        'workflowClass' => $workflowClass,
                        'subjectClass' => $subjectClass,
                        'isActive' => 1,
                    );
                    
                    $wf_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfWorkflow')->findOneBy($criteria);
                    
                    if ($wf_entity == null) {
                        $entity = new NmtWfWorkflow();
                        $entity->setWorkflowName($workflowName);
                        $entity->setWorkflowFactory($workflowFactory);
                        $entity->setWorkflowClass($workflowClass);
                        $entity->setSubjectClass($subjectClass);
                        $entity->setIsActive(1);
                        
                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());
                        $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                        
                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                        $wf_entity = $entity;
                    }
                    $list[] = $wf_entity;
                    
                    /** @var \Symfony\Component\Workflow\Workflow $w */
                    $w = $v->createWorkflow();
                    
                    $transitions = $w->getDefinition()->getTransitions();
                    $places = $w->getDefinition()->getPlaces();
                    foreach ($transitions as $t) {
                        
                        $transitionName = $t->getName();
                        $criteria = array(
                            'workflow' => $wf_entity,
                            'transitionName' => $transitionName
                        );
                        
                        $transition_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfTransition')->findOneBy($criteria);
                        
                        if ($transition_entity == null) {
                            $new_t_entity = new NmtWfTransition();
                            $new_t_entity->setTransitionName($transitionName);
                            $new_t_entity->setIsActive(1);
                            $new_t_entity->setWorkflow($wf_entity);
                            $new_t_entity->setWorkflowName($workflowName);
                            $new_t_entity->setCreatedBy($u);
                            $new_t_entity->setCreatedOn(new \DateTime());
                            $new_t_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                            
                            $this->doctrineEM->persist($new_t_entity);
                            $this->doctrineEM->flush();
                        }
                    }
                    
                    foreach ($places as $p) {
                        $placeName = $p;
                        $criteria = array(
                            'workflow' => $wf_entity,
                            'placeName' => $placeName
                        );
                        $place_entity = $this->doctrineEM->getRepository('Application\Entity\NmtWfPlace')->findOneBy($criteria);
                        if ($place_entity == null) {
                            $new_p_entity = new NmtWfPlace();
                            $new_p_entity->setPlaceName($placeName);
                            $new_p_entity->setIsActive(1);
                            $new_p_entity->setWorkflow($wf_entity);
                            $new_p_entity->setCreatedBy($u);
                            $new_p_entity->setCreatedOn(new \DateTime());
                            $new_p_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                            
                            $this->doctrineEM->persist($new_p_entity);
                            $this->doctrineEM->flush();
                        }
                    }
                }
            }
        }
        
        return new ViewModel(array(
            'wf_list' => $list
        ));
    }
    
    
    /**
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function transitionAction()
    {
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'workflow' => $target_id,
            
        );
    
        $sort_criteria = array();
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtWfTransition')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
              
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
        ));
    }

    
    public function deleteAction()
    {
        $this->nmtWfWorkflowTable->fetchAll();
        var_dump($this->nmtWfWorkflowTable->fetchAll());
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
