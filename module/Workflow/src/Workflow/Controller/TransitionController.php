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
use Application\Entity\NmtWfTransitionAgent;

/**
 *
 * @author nmt
 *        
 */
class TransitionController extends AbstractActionController
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

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAgentAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $target_id = (int) $request->getPost('target_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $target_id,
                'token' => $token
            );
            
            //echo "/workflow/transition/add-agent?target_id=" . $target_id . "&token=" . $token;
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtWfTransition')->findOneBy($criteria);
            if ($target == null) {
                return $this->redirect()->toRoute('access_denied');
            }
            
            $agents = $request->getPost('agents');
            if (count($agents) > 0) {
                
                foreach ($agents as $a) {
                    
                    // if not granted
                    $criteria = array(
                        'transition' => $target_id,
                        'agent' => $a
                    );
                    
                    $isGranted = $this->doctrineEM->getRepository('Application\Entity\NmtWfTransitionAgent')->findBy($criteria);
                    if (count($isGranted) == 0) {
                        $entity = new NmtWfTransitionAgent();
                        
                        $entity->setTransition($target);
                        
                        $agent = $this->doctrineEM->find('Application\Entity\MlaUsers', $a);
                        $entity->setAgent($agent);
                        
                        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                            "email" => $this->identity()
                        ));
                        
                        $entity->setCreatedBy($u);
                        $entity->setCreatedOn(new \DateTime());
                        
                        $this->doctrineEM->persist($entity);
                        $this->doctrineEM->flush();
                        return $this->redirect()->toUrl("/workflow/transition/add-agent?target_id=" . $target_id . "&token=" . $token);
                    }
                }
            }
        }
        
        // NO POST
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtWfTransition')->findOneBy($criteria);
        
        if ($target !== null) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $entity = new NmtWfTransitionAgent();
                
                $entity->setAgent();
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
            }
            
            $agents = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->getOtherAgentOfWfTransition($target_id);
            $total_records = count($agents);
            // $this->redirect ()->toUrl ( 'home' );
            return new ViewModel(array(
                'total_records' => $total_records,
                'target' => $target,
                'agents' => $agents
                // 'paginator' => $paginator
            ));
        }
        
        return $this->redirect()->toRoute('access_denied');
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
