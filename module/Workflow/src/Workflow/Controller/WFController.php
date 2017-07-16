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

/*
 * Control Panel Controller
 */
class WFController extends AbstractActionController
{

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
        $wf_list = array();
        
        $entity = new NmtProcurePr();
        
        $subjects = $this->wfService->getSupportedSubjects();
        foreach ($subjects as $s) {
            
            /** @var \Workflow\Workflow\WorkflowFactoryInterface $wf_factory */
            $wf_factory = $this->wfService->getWorkFlowFactory($s);
            
            if (! $wf_factory == null) {
                /** @var \Workflow\Workflow\AbstractWorkflow  $wf */
                $wf_list = $wf_factory->getWorkFlowList();
                
                foreach ($wf_list as $k => $v) {
                    
                    // echo get_class($wf->getWorkflowFactory());
                    // echo get_class($wf->getWorkflowName());
                    
                    /** @var \Symfony\Component\Workflow\Workflow $w */
                    $w = $v->createWorkflow();
                    
                    $transitions = $w->getDefinition()->getTransitions();
                    $places = $w->getDefinition()->getPlaces();
                    echo $w->getName() . '<br>';
                    
                    foreach ($places as $p) {
                        echo $p . ';';
                    }
                    
                    foreach ($transitions as $t) {
                        echo $t->getName() . '<br>';
                        foreach ($t->getFroms() as $f) {
                            echo 'From: ' . $f . '<br>';
                        }
                        
                        foreach ($t->getTos() as $f) {
                            echo 'To: ' . $f . '<br>';
                        }
                    }
                }
            }
        }
        
        /*
         * $wf = $this->ProcureWfPlugin()->getWF($entity);
         * $dumper = new GraphvizDumper();
         * $imageContent = $dumper->dump($wf->getDefinition());
         * echo $imageContent;
         */
        
        /*
         * $response = $this->getResponse();
         * $imageContent = file_get_contents($pic->url);
         * $response->setContent($imageContent);
         * $response->getHeaders()
         * ->addHeaderLine('Content-Transfer-Encoding', 'binary')
         * ->addHeaderLine('Content-Type', 'image/png')
         * ->addHeaderLine('Content-Length', $imageContent);
         */
        
        // return $response;
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
