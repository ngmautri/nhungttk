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
use Workflow\Model\NmtWfWorkflowTable;
use Workflow\Model\NmtWfWorkflow;
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
        $entity = new NmtProcurePr();
        $wf = $this->ProcureWfPlugin()->getWF($entity);
        $dumper = new GraphvizDumper();
        $imageContent = $dumper->dump($wf->getDefinition());
        echo $imageContent;
        
      /*   $response = $this->getResponse();
        $imageContent = file_get_contents($pic->url);
        $response->setContent($imageContent);
        $response->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', 'image/png')
            ->addHeaderLine('Content-Length', $imageContent); */
        
        //return $response;
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
