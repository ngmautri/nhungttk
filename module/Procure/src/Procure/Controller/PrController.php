<?php

/**
 * Procurment Module
 */
namespace Procure\Controller;

use Symfony\Component\Workflow\Exception\LogicException;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtPmProject;
use Zend\Validator\Date;
use Zend\Math\Rand;
use Application\Entity\NmtProcurePr;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Application\Entity\NmtProcurePrRow;
use Endroid\QrCode\QrCode;
use Application\Service\PdfService;
use Zend\Http\Client as HttpClient;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ__";
    const QR_CODE_PATH = "/data/procure/qr_code/pr/";

    protected $doctrineEM;
    protected $pdfService;

    /*
     * Defaul Action
     */
    public function indexAction()
    {
        $client = new HttpClient();
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');
        
        $method = $this->params()->fromQuery('method', 'get');
        $client->setUri('http://localhost:81/procure/pr-rest');
   
          switch ($method) {
            case 'get':
                
                $response = $this->getResponse();
                
                $client->setMethod('GET');
                $client->setParameterGET(array(
                    'id' => 1
                ));
                break;
            
            case 'get-list':
                $client->setMethod('GET');
                break;
            case 'create':
                $client->setMethod('POST');
                $client->setParameterPOST(array(
                    'name' => 'samsonasik'
                ));
                break;
            case 'update':
                $data = array(
                    'name' => 'ikhsan'
                );
                $adapter = $client->getAdapter();
                
                $adapter->connect('localhost', 80);
                $uri = $client->getUri() . '?id=1';
                // send with PUT Method, with $data parameter
                $adapter->write('PUT', new \Zend\Uri\Uri($uri), 1.1, array(), http_build_query($data));
                
                $responsecurl = $adapter->read();
                list ($headers, $content) = explode("\r\n\r\n", $responsecurl, 2);
                $response = $this->getResponse();
                
                $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');
                $response->setContent($content);
                
                return $response;
            case 'delete':
                $adapter = $client->getAdapter();
                
                $adapter->connect('localhost', 80);
                $uri = $client->getUri() . '?id=1'; // send parameter id = 1
                                                  // send with DELETE Method
                $adapter->write('DELETE', new \Zend\Uri\Uri($uri), 1.1, array());
                
                $responsecurl = $adapter->read();
                list ($headers, $content) = explode("\r\n\r\n", $responsecurl, 2);
                $response = $this->getResponse();
                
                $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');
                $response->setContent($content);
                
                return $response;
        }
        
        // if get/get-list/create
        $response = $client->send();
        if (! $response->isSuccess()) {
            // report failure
            $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
            $message = $message . $client->getMethod();
            $message = $message . "NO....";
            
            
            $response = $this->getResponse();
            $response->setContent($message);
            return $response;
        }
        $body = $response->getBody();
        
        $response = $this->getResponse();
        $response->setContent($body);
        
        return $response;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();
        
        // var_dump($criteria);
        
        $sort_criteria = array();
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;
        
        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtPmProject')->findBy($criteria, $sort_criteria);
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setChecksum(md5(uniqid("project_" . $entity->getId()) . microtime()));
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }
        
        $this->doctrineEM->flush();
        
        /**
         *
         * @todo : update index
         */
        // $this->employeeSearchService->createEmployeeIndex();
        
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtPmProject')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

   /**
    * Add new PR
    * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
    */
    public function addAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $prNumber = $request->getPost('prNumber');
            $prName = $request->getPost('prName');
            $keywords = $request->getPost('keywords');
            $submittedOn = $request->getPost('submittedOn');
            
            $totalRowManual = (int) $request->getPost('$totalRowManual');
            
            $remarks = $request->getPost('remarks');
            $isDraft = $request->getPost('isDraft');
            $isActive = $request->getPost('isActive');
            $status = $request->getPost('status');
            $remarks = $request->getPost('remarks');
            $department_id = $request->getPost('department_id');
            
            if ($isActive != 1) {
                $isActive = 0;
            }
            
            if ($isDraft != 1) {
                $isDraft = 0;
            }
            
            $prAutoNumber = 'later';
            
            if ($prNumber == null) {
                $errors[] = 'Please enter PR Number!';
            }
            
            if ($prName == null) {
                $errors[] = 'Please enter PR Name!';
            }
            
            $entity = new NmtProcurePr();
            $entity->setPrAutoNumber($prAutoNumber);
            $entity->setPrNumber($prNumber);
            $entity->setPrName($prName);
            $entity->setKeywords($keywords);
            $entity->setRemarks($remarks);
            $entity->setIsActive($isActive);
            $entity->setIsDraft($isDraft);
            $entity->setStatus($status);
            $entity->setTotalRowManual($totalRowManual);
            
            $validator = new Date();
            
            // Empty is OK
            if ($submittedOn !== null) {
                if ($submittedOn !== "") {
                    
                    if (! $validator->isValid($submittedOn)) {
                        $errors[] = 'Date is not correct or empty!';
                    } else {
                        $entity->setSubmittedOn(new \DateTime($submittedOn));
                    }
                }
            }
            
            if ($department_id > 0) {
                $department = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $department_id);
                $entity->setDepartment($department);
            }
            
            if (count($errors) > 0) {
                return new ViewModel(array(
                    
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            }
            
            // NO ERROR
            // ++++++++++++++++++++++++++
            
            $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            $createdOn = new \DateTime();
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn($createdOn);
            $entity->setChecksum(md5(uniqid("pr_". microtime())));
            
             // generate document
            // ==================
            $criteria = array(
                'isActive' => 1,
                'subjectClass' => get_class($entity)
            );
            
            /** @var \Application\Entity\NmtApplicationDocNumber $docNumber ; */
            $docNumber = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);
            if ($docNumber != null) {
                $maxLen = strlen($docNumber->getToNumber());
                $currentLen = 1;
                $currentDoc = $docNumber->getPrefix();
                $current_no = $docNumber->getCurrentNumber();
                
                if ($current_no == null) {
                    $current_no = $docNumber->getFromNumber();
                } else {
                    $current_no ++;
                    $currentLen = strlen($current_no);
                }
                
                $docNumber->setCurrentNumber($current_no);
                
                $tmp = "";
                for ($i = 0; $i < $maxLen - $currentLen; $i ++) {
                    
                    $tmp = $tmp . "0";
                }
                
                $currentDoc = $currentDoc . $tmp . $current_no;
                $entity->setPrAutoNumber($currentDoc);
            }
            
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
                        
            $m = sprintf('PR #%s - %s created. OK!',$entity->getId(), $entity->getPrAutoNumber());
            $this->flashMessenger()->addMessage($m);
            
            //Trigger: procure.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn
            ));
            
            // create QR_CODE
            $redirectUrl = "/procure/pr/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId() . "&checksum=" . $entity->getChecksum();
            
            // $name_part1 = Rand::getString ( 6, self::CHAR_LIST, true ) . "_" . Rand::getString ( 10, self::CHAR_LIST, true );
            $qr_code_name = $entity->getChecksum() . '_' . $entity->getToken() . '_' . $entity->getId() . '.png';
            $folder_relative = $qr_code_name[0] . $qr_code_name[1] . DIRECTORY_SEPARATOR . $qr_code_name[2] . $qr_code_name[3] . DIRECTORY_SEPARATOR . $qr_code_name[4] . $qr_code_name[5] . DIRECTORY_SEPARATOR . $qr_code_name[6] . $qr_code_name[7];
            
            $folder = ROOT . self::QR_CODE_PATH . DIRECTORY_SEPARATOR . $folder_relative . DIRECTORY_SEPARATOR;
            
            if (! is_dir($folder)) {
                mkdir($folder, 0777, true); // important
            }
            
            $qrCode = new QrCode($redirectUrl);
            $qrCode->setSize(100);
            $qrCode->writeFile($folder . $qr_code_name);
            
            $redirectUrl = "/procure/pr/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId() . "&checksum=" . $entity->getChecksum();
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NO POST
        // ++++++++++++++++++++++++++
        
        $redirectUrl= null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $redirectUrl = $this->getRequest()
        ->getHeader('Referer')
        ->getUri();
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => null
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $this->layout("layout/fluid");
        
        $criteria = array();
        
        // var_dump($criteria);
        
        $sort_criteria = array();
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;
        
        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function allAction()
    {
        // $this->layout ( "layout/fluid" );
        // $plugin = $this->ProcureWfPlugin();
        // echo($plugin->getWF());
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $pr_year = $this->params()->fromQuery('pr_year');
        
        $is_active = (int) $this->params()->fromQuery('is_active');
        
        $status = $this->getEvent()
            ->getRouteMatch()
            ->getParam("status");
        $row_number = (int) $this->getEvent()
            ->getRouteMatch()
            ->getParam("row_number");
        
        if ($is_active == null) :
            $is_active = 1;
		endif;
        
        if ($balance == null) :
            $balance = 1;
		endif;
        
        if ($status == "pending") {
            $balance = 1;
        } elseif ($status == "completed") {
            $balance = 0;
        } elseif ($status == "all") {
            $balance = 2;
        }
        
        // echo $balance;
        
        if ($row_number == 0) {
            if ($sort_by == null) :
                $sort_by = "submittedOn"; endif;
            
            if ($sort == null) :
                $sort = "DESC";endif;
            
        } else {
            if ($sort_by == null) :
                $sort_by = "submittedOn";
                
                if ($sort == null) :
                    $sort = "DESC";
            endif;
            endif;
                
            
        }
        
        if ($pr_year == null) :
            // $pr_year = date('Y');
            $pr_year = 0;
        endif;
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;
        
        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;
        
        /** @var \Doctrine\ORM\EntityManager $doctrineEM ;*/
        $doctrineEM = $this->NmtPlugin()->doctrineEM();
        
        /** @var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        
        $list = $res->getPrList($row_number, $pr_year, $is_active, $balance, $sort_by, $sort, 0, 0);
        
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $res->getPrList($row_number, $pr_year, $is_active, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'balance' => $balance,
            'is_active' => $is_active,
            'status' => $status,
            'pr_year' => $pr_year,
            'row_number' => $row_number
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();
        
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        // $u = $this->doctrineEM->getRepository( 'Application\Entity\MlaUsers')->findOneBy(array("email"=>$this->identity() ));
        
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $id = (int) $this->params()->fromQuery('entity_id');
        // $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $pr = $res->getPR($id, $token);
        
        if ($pr == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity = null;
        if ($pr[0] instanceof NmtProcurePr) {
            $entity = $pr[0];
        }
        
        if ($entity !== null) {
            
            try {
                /** @var \Symfony\Component\Workflow\Workflow $wf */
                // $wf = $this->ProcureWfPlugin()->createWorkflow($entity);
                
                // var_dump($wf->getEnabledTransitions($entity));
                
                // $wf->apply($entity, "recall");
                // $dumper = new GraphvizDumper();
                // echo $dumper->dump($wf->getDefinition());
                
                /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
                $wf_plugin = $this->WfPlugin();
                
                /** @var \Workflow\Service\WorkflowService $wfService */
                $wfService = $wf_plugin->getWorkflowSerive();
                
                /** @var \Workflow\Workflow\Procure\Factory\PrWorkflowFactoryAbstract $wf_factory */
                $wf_factory = $wfService->getWorkFlowFactory($entity);
            
            /** @var \Symfony\Component\Workflow\Workflow  $wf */
                // $wf = $wf_factory->makePrSendingWorkflow()->createWorkflow();
                // $wf->apply($entity,"send");
            } catch (LogicException $e) {
                // echo $e->getMessage();
            }
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'total_row' => $pr['total_row'],
                'max_row_number' => $pr['max_row_number'],
                'active_row' => $pr['active_row']
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function show1Action()
    {
        $request = $this->getRequest();
        $redirectUrl = null;
        
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
        $id = (int) $this->params()->fromQuery('entity_id');
        // $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $pr = $res->getPR($id, $token);
        
        if ($pr == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity = null;
        if ($pr[0] instanceof NmtProcurePr) {
            $entity = $pr[0];
        }
        
        if ($entity !== null) {
            
            try {
                /** @var \Symfony\Component\Workflow\Workflow $wf */
                // $wf = $this->ProcureWfPlugin()->createWorkflow($entity);
                
                // var_dump($wf->getEnabledTransitions($entity));
                
                // $wf->apply($entity, "recall");
                // $dumper = new GraphvizDumper();
                // echo $dumper->dump($wf->getDefinition());
                
                /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
                $wf_plugin = $this->WfPlugin();
                
                /** @var \Workflow\Service\WorkflowService $wfService */
                $wfService = $wf_plugin->getWorkflowSerive();
                
                /** @var \Workflow\Workflow\Procure\Factory\PrWorkflowFactoryAbstract $wf_factory */
                $wf_factory = $wfService->getWorkFlowFactory($entity);
            
            /** @var \Symfony\Component\Workflow\Workflow  $wf */
                // $wf = $wf_factory->makePrSendingWorkflow()->createWorkflow();
                // $wf->apply($entity,"send");
            } catch (LogicException $e) {
                // echo $e->getMessage();
            }
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'total_row' => $pr['total_row'],
                'max_row_number' => $pr['max_row_number'],
                'active_row' => $pr['active_row']
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function submitAction()
    {
        /*
         * $request = $this->getRequest();
         *
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('access_denied');
         * }
         */
        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $rows = $res->downloadPrRows($id, $token);
        
        if ($rows == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
        $wf_plugin = $this->WfPlugin();
        
        /** @var \Workflow\Service\WorkflowService $wfService */
        $wfService = $wf_plugin->getWorkflowSerive();
        
        /** @var \Application\Entity\NmtProcurePr $pr ; */
        $pr = null;
        if ($rows[0][0] instanceof NmtProcurePrRow) {
            
            $pr = $rows[0][0]->getPr();
        }
        
        if ($pr == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            try {
                
                /** @var \Workflow\Workflow\Procure\Factory\PrWorkflowFactoryAbstract $pr_wf_factory */
                $pr_wf_factory = $wfService->getWorkFlowFactory($pr);
                
                /** @var \Symfony\Component\Workflow\Workflow  $wf */
                $wf = $pr_wf_factory->makePrSendingWorkflow()->createWorkflow();
                $wf->apply($pr, "submit");
            } catch (LogicException $e) {
                $this->flashMessenger()->addMessage($e->getMessage());
                $url = "/procure/pr/show?token=" . $token . "&entity_id=" . $id;
                // return $this->redirect()->toUrl($url);
            }
        }
        
        foreach ($rows as $r) {
            /** @var \Application\Entity\NmtProcurePrRow $entity */
            $entity = $r[0];
            $errors = array();
            
            try {
                
                /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
                $wf_plugin = $this->WfPlugin();
                
                /** @var \Workflow\Service\WorkflowService $wfService */
                $wfService = $wf_plugin->getWorkflowSerive();
                
                /** @var \Workflow\Workflow\Procure\Factory\PrRowWorkflowFactoryAbstract $wf_factory */
                $wf_factory = $wfService->getWorkFlowFactory($entity);
                
                /** @var \Symfony\Component\Workflow\Workflow  $wf */
                $wf = $wf_factory->makePrRowWorkFlow()->createWorkflow();
                $wf->apply($entity, "submit");
            } catch (LogicException $e) {
                // echo $e->getMessage();
                $errors[] = $e->getMessage();
            }
        }
        
        if (count($errors) > 0) {
            $m = "<ul>";
            foreach ($errors as $error) {
                $m = $m . "<li>" . $error . "</li>";
            }
            $m = $m . "</ul>";
            
            $this->flashMessenger()->addMessage($m);
        } else {
            $this->flashMessenger()->addMessage("PR is submited!");
        }
        
        $url = "/procure/pr/show?token=" . $token . "&entity_id=" . $id;
        // return $this->redirect()->toUrl($url);
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function grAction()
    {
        // $plugin = $this->ProcureWfPlugin();
        // $wf = $plugin->getWF();
        $request = $this->getRequest();
        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        // $u = $this->doctrineEM->getRepository( 'Application\Entity\MlaUsers')->findOneBy(array("email"=>$this->identity() ));
        
        $id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        if ($entity !== null) {
            
            try {
            /** @var \Symfony\Component\Workflow\Workflow $wf */
                // $wf = $this->ProcureWfPlugin()->createWorkflow($entity);
                
                // var_dump($wf->getEnabledTransitions($entity));
                
                // $wf->apply($entity, "recall");
                // $dumper = new GraphvizDumper();
                // echo $dumper->dump($wf->getDefinition());
            
            /** @var \Workflow\Controller\Plugin\WfPlugin $wf_plugin */
                // $wf_plugin = $this->WfPlugin();
            
            /** @var \Workflow\Service\WorkflowService $wfService */
                // $wfService = $wf_plugin->getWorkflowSerive();
            
            /** @var \Workflow\Workflow\Procure\Factory\PrWorkflowFactoryAbstract $wf_factory */
                // $wf_factory = $wfService->getPrWorkFlowFactory($entity);
            
            /** @var \Symfony\Component\Workflow\Workflow  $wf */
                // $wf = $wf_factory->makePrSendingWorkflow();
                // $wf->apply($entity,"get");
            } catch (LogicException $e) {
                // echo $e->getMessage();
            }
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            
            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = $request->getPost('entity_id');
            $token = $request->getPost('token');
            $nTry= $request->getPost('n');
            
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\NmtProcurePr $entity ;*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Entity not found';
                $this->flashMessenger()->addMessage('Something went wrong!');
                
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            } else {
                
                $oldEntity= clone($entity);
                
                $prNumber = $request->getPost('prNumber');
                $prName = $request->getPost('prName');
                $keywords = $request->getPost('keywords');
                $remarks = $request->getPost('remarks');
                $isDraft = $request->getPost('isDraft');
                $isActive = $request->getPost('isActive');
                $status = $request->getPost('status');
                $remarks = $request->getPost('remarks');
                $department_id = $request->getPost('department_id');
                $submittedOn = $request->getPost('submittedOn');
                
                $totalRowManual = (int) $request->getPost('totalRowManual');
                
                if ($isActive != 1) {
                    $isActive = 0;
                }
                
                if ($isDraft != 1) {
                    $isDraft = 0;
                }
                
                if ($prNumber == null) {
                    $errors[] = 'Please enter PR Number!';
                }
                
                if ($prName == null) {
                    $errors[] = 'Please enter PR Name!';
                }
                 
                $entity->setPrNumber($prNumber);
                $entity->setPrName($prName);
                $entity->setKeywords($keywords);
                $entity->setRemarks($remarks);
                $entity->setIsActive($isActive);
                $entity->setIsDraft($isDraft);
                $entity->setStatus($status);
                $entity->setTotalRowManual($totalRowManual);
                if ($department_id > 0) {
                    $department = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $department_id);
                    $entity->setDepartment($department);
                }
                
                $validator = new Date();
                
                // Empty is OK
                if ($submittedOn !== null) {
                    if ($submittedOn !== "") {
                        
                        if (! $validator->isValid($submittedOn)) {
                            $errors[] = 'Date is not correct or empty!';
                        } else {
                            $entity->setSubmittedOn(new \DateTime($submittedOn));
                        }
                    }
                }
                
                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
                
                if(count($changeArray)==0){
                    $nTry++;
                    $errors[] = sprintf('Nothing changed! n = %s',$nTry);
                }
                
                if($nTry >=3){
                    $errors[] = sprintf('Do you really want to edit "%s (%s)"?',$entity->getPrName(),$entity->getPrAutoNumber());
                }
                
                if($nTry == 5){
                    $m=sprintf('You might be not ready to edit "%s (%s)". Please try later!',$entity->getPrName(),$entity->getPrAutoNumber());
                    $this->flashMessenger()->addMessage($m);
                    return $this->redirect()->toUrl($redirectUrl);
                }
                
                if (count($errors) > 0) {
                    return new ViewModel(array(
                        
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity,
                        'n'=>$nTry,                        
                    ));
                }
                
                // NO ERROR
                //========================
                $changeOn = new \DateTime();
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setRevisionNo($entity->getRevisionNo() + 1);                
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                $m = sprintf('"PR #%s - %s" updated. Change No.:%s. OK!', $entity->getId(),$entity->getPrAutoNumber(),count($changeArray));
                
                
                // Trigger Change Log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('procure.change.log', __METHOD__, array(
                    'priority' => 7,
                    'message' => $m,
                    'objectId' => $entity->getId(),
                    'objectToken' => $entity->getToken(),
                    'changeArray' => $changeArray,
                    'changeBy' => $u,
                    'changeOn' => $changeOn,
                    'revisionNumber' => $entity->getRevisionNo(),
                    'changeDate' => $changeOn,
                    'changeValidFrom' => $changeOn,
                ));
                
                // Trigger Activity Log . AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $changeOn
                ));
                
                
                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST
        //========================
       
        $redirectUrl=null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        if ($entity instanceof \Application\Entity\NmtProcurePr) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'n'=>0,
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return void|\Zend\Stdlib\ResponseInterface
     */
    public function getPqCodePngAction()
    {
        $id = (int) $this->params()->fromQuery('id');
        $token = $this->params()->fromQuery('token');
        
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtProcurePr $entity ;*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        
        if ($entity !== null) {
            
            $qr_code_name = $entity->getChecksum() . '_' . $entity->getToken() . '_' . $entity->getId() . '.png';
            $folder_relative = $qr_code_name[0] . $qr_code_name[1] . DIRECTORY_SEPARATOR . $qr_code_name[2] . $qr_code_name[3] . DIRECTORY_SEPARATOR;
            $folder_relative = $folder_relative . $qr_code_name[4] . $qr_code_name[5] . DIRECTORY_SEPARATOR . $qr_code_name[6] . $qr_code_name[7];
            
            $folder = ROOT . self::QR_CODE_PATH . $folder_relative . DIRECTORY_SEPARATOR . $qr_code_name;
            if (! file_exists($folder)) {
                return;
            }
            // echo $folder;
            
            $imageContent = file_get_contents($folder);
            $response = $this->getResponse();
            $response->setContent($imageContent);
            $response->getHeaders()
                ->addHeaderLine('Content-Transfer-Encoding', 'binary')
                ->addHeaderLine('Content-Type', 'image/png')
                ->addHeaderLine('Content-Length', mb_strlen($imageContent));
            return $response;
        } else {
            return;
        }
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function printPdfAction()
    {
        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtProcurePr $entity ;*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        
        if ($entity !== null) {
            
            $qr_code_name = $entity->getChecksum() . '_' . $entity->getToken() . '_' . $entity->getId() . '.png';
            $folder_relative = $qr_code_name[0] . $qr_code_name[1] . DIRECTORY_SEPARATOR . $qr_code_name[2] . $qr_code_name[3] . DIRECTORY_SEPARATOR;
            $folder_relative = $folder_relative . $qr_code_name[4] . $qr_code_name[5] . DIRECTORY_SEPARATOR . $qr_code_name[6] . $qr_code_name[7];
            
            /*
             * $folder = ROOT . self::QR_CODE_PATH . $folder_relative . DIRECTORY_SEPARATOR . $qr_code_name;
             * if (! file_exists($folder)) {
             * return;
             * }
             */
            
            // echo $folder;
            
            $details = 'If you can see this PDF file, the PDF service has been configurated successfully! :-)';
            $image_file = '';
            // $image_file = $folder;
            $content = $this->pdfService->printPrPdf($details, $image_file);
            
            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/x-pdf');
            $response->setContent($content);
            return $response;
        } else {
            return;
        }
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \PM\Controller\IndexController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getPdfService()
    {
        return $this->pdfService;
    }

    /**
     *
     * @param mixed $pdfService
     */
    public function setPdfService(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }
}
