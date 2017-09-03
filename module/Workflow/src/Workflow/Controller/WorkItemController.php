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
use Symfony\Component\Workflow\Exception\LogicException;
use Application\Entity\NmtWfWorkflow;
use Zend\Math\Rand;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtWfTransition;
use Application\Entity\NmtWfPlace;
use Application\Entity\NmtProcurePrRow;

/*
 * Control Panel Controller
 */
class WorkItemController extends AbstractActionController
{

    const CHAR_LIST = "___0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ___";

    protected $doctrineEM;

    protected $wfService;

    public function indexAction()
    {}

    public function applyAction()
    {
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Entity\NmtWfWorkitem $wi ;*/
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        $wi = $this->doctrineEM->getRepository('Application\Entity\NmtWfWorkitem')->findOneBy($criteria);
        if ($wi == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $subject_class = $wi->getSubjectClass();
        $subject = null;
        switch ($subject_class) {
            case get_class(new NmtProcurePrRow()):
                $criteria = array(
                    'id' => $wi->getSubjectId()
                );
                
                /**@var \Application\Entity\NmtProcurePrRow $subject ;*/
                $subject = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);
                break;
            case get_class(new NmtProcurePr()):
                break;
        }
        
        $error = null;
        try {
            /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
            $wf_plugin = $this->WfPlugin();
            
            /** @var \Workflow\Service\WorkflowService $wfService */
            $wfService = $wf_plugin->getWorkflowSerive();
            
            /** @var \Workflow\Workflow\Procure\Factory\PrRowWorkflowFactoryAbstract $pr_row_factory */
            $pr_row_factory = $wfService->getWorkFlowFactory($subject);
            
            /** @var \Symfony\Component\Workflow\Workflow  $wf */
            $wf = $pr_row_factory->makePrRowWorkFlow()->createWorkflow();
            $wf->apply($subject, $wi->getTransitionName());
            
        } catch (LogicException $e) {
            $error = $e->getMessage();
        }
        
        return new ViewModel(array(
            'error' => $error,
        ));
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

    /**
     *
     * @return \Workflow\Service\WorkflowService
     */
    public function getWfService()
    {
        return $this->wfService;
    }

    /**
     *
     * @param WorkflowService $wfService
     */
    public function setWfService(WorkflowService $wfService)
    {
        $this->wfService = $wfService;
    }
}
