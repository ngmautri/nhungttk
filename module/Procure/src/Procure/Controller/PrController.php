<?php
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
use Zend\Http\Request;
use Ramsey\Uuid;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrController extends AbstractActionController
{

    const QR_CODE_PATH = "/data/procure/qr_code/pr/";

    protected $doctrineEM;

    protected $pdfService;

    protected $prService;
    
    protected $attachmentService;
    

    /*
     * Defaul Action
     */
    public function indexAction()
    {
        $client = new HttpClient();
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');

        $method = $this->params()->fromQuery('method', 'get');

        switch ($method) {
            case 'get':

                $response = $this->getResponse();
                $client->setUri('http://localhost:8983/solr/inventory_item/select?rows=1000');
                $client->setMethod('GET');
                $client->setParameterGET(array(
                    'q' => "name:kim"
                ));
                break;

            case 'get-list':
                $client->setMethod('GET');
                $client->setUri('http://localhost:8983/solr/inventory_item/select');
                break;
            case 'create':

                $data = array(
                    "name" => "Laos Finance manager",
                    "name" => "kim"
                );

                $request = new Request();
                $request->setUri('http://localhost:8983/solr/inventory_item/update/json/docs?commit=true');
                $request->setMethod('POST');
                $request->setContent(json_encode($data));

                $request->getHeaders()->addHeaders(array(
                    'Content-Type' => 'application/json'
                ));

                // $client->setHeaders('Content-type','application/json');
                $client->setEncType(HttpClient::ENC_FORMDATA);

                // if get/get-list/create
                $response = $client->send($request);

                if (! $response->isSuccess()) {
                    // report failure
                    $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
                    $message .= $response->getContent();
                    $message = $message . $client->getMethod();
                    $message = $message . "...........NO unknown....";

                    $response = $this->getResponse();
                    $response->setContent($message);
                    return $response;
                }

                $body = $response->getBody();

                $response = $this->getResponse();
                $response->setContent($body);

                return $response;

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

        $response = $client->send();

        if (! $response->isSuccess()) {
            // report failure
            $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
            $message = $message . $client->getMethod();
            $message = $message . "...........NO unknown....";

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
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
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
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function priceMatchingAction()
    {
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     * Add new PR
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        // Is Posting .................
        // ============================

        if ($request->isPost()) {
            $errors = array();
            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $entity = new NmtProcurePr();
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $errors = $this->prService->saveHeader($entity, $data, $u, TRUE, __METHOD__);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/pr/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] PR #%s - %s created', $entity->getId(), $entity->getPrAutoNumber());

            // create QR_CODE
            $redirectUrl = "/procure/pr/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId() . "&checksum=" . $entity->getChecksum();

            // $name_part1 = Rand::getString ( 6, \Application\Model\Constants::CHAR_LIST, true ) . "_" . Rand::getString ( 10, \Application\Model\Constants::CHAR_LIST, true );
            $qr_code_name = $entity->getChecksum() . '_' . $entity->getToken() . '_' . $entity->getId() . '.png';
            $folder_relative = $qr_code_name[0] . $qr_code_name[1] . DIRECTORY_SEPARATOR . $qr_code_name[2] . $qr_code_name[3] . DIRECTORY_SEPARATOR . $qr_code_name[4] . $qr_code_name[5] . DIRECTORY_SEPARATOR . $qr_code_name[6] . $qr_code_name[7];

            $folder = ROOT . self::QR_CODE_PATH . DIRECTORY_SEPARATOR . $folder_relative . DIRECTORY_SEPARATOR;

            if (! is_dir($folder)) {
                mkdir($folder, 0777, true); // important
            }

            $qrCode = new QrCode($redirectUrl);
            $qrCode->setSize(100);
            $qrCode->writeFile($folder . $qr_code_name);

            $redirectUrl = "/procure/pr-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId() . "&checksum=" . $entity->getChecksum();

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // ++++++++++++++++++++++++++

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $entity = new NmtProcurePr();
        $entity->setIsActive(1);
        $entity->setIsDraft(1);
        $entity->setWarehouse($u->getCompany()
            ->getDefaultWarehouse());
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'nmtPlugin' => $nmtPlugin
        ));
        $viewModel->setTemplate("procure/pr/crud");
        return $viewModel;
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

        // $this->layout("Procure/layout-fluid-1");

        // echo \Application\Model\Constants::v4();
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
            // 'uid'=>\Application\Model\Constants::v4(),
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

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
        $pr = $res->getPrNew($id, $token);

        if ($pr == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($pr[0] instanceof NmtProcurePr) {
            $entity = $pr[0];
        }

        if ($entity instanceof \Application\Entity\NmtProcurePr) {

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
                'active_row' => $pr['active_row'],
                'total_attachment' => $pr['total_attachment'],
                'total_picture' => $pr['total_picture'],
                'nmtPlugin' => $nmtPlugin
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }
    
    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $request = $this->getRequest();
        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        
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
        $pr = $res->getPrNew($id, $token);
        
        if ($pr == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $entity = null;
        if ($pr[0] instanceof NmtProcurePr) {
            $entity = $pr[0];
        }
        
        if ($entity instanceof \Application\Entity\NmtProcurePr) {
            
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
                'active_row' => $pr['active_row'],
                'total_attachment' => $pr['total_attachment'],
                'total_picture' => $pr['total_picture'],
                'nmtPlugin' => $nmtPlugin
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }
    

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $this->layout("Procure/layout-fullscreen");

        // $u = $this->doctrineEM->getRepository( 'Application\Entity\MlaUsers')->findOneBy(array("email"=>$this->identity() ));

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
            
        $id = (int) $this->params()->fromQuery('entity_id');
        // $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $pr = $res->getPrNew($id, $token);

        if ($pr == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($pr[0] instanceof NmtProcurePr) {
            $entity = $pr[0];
        }

        if ($entity instanceof \Application\Entity\NmtProcurePr) {

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
                'active_row' => $pr['active_row'],
                'total_attachment' => $pr['total_attachment'],
                'total_picture' => $pr['total_picture']
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
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $entity_id = (int) $data['entity_id'];
            $token = $data['entity_token'];
            $nTry = $data['n'];

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtProcurePr $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity not found';
                $this->flashMessenger()->addMessage('Something went wrong!');

                return new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry,
                    'nmtPlugin' => $nmtPlugin
                ));
            }

            $this->prService->saveHeader($entity, $data, $u, false, __METHOD__);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/pr/crud");
                return $viewModel;
            }

            $m = sprintf('"PR #%s - %s" updated', $entity->getId(), $entity->getPrAutoNumber());

            $this->flashMessenger()->addMessage($m);
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // ========================

        $redirectUrl = null;
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
        if (! $entity instanceof \Application\Entity\NmtProcurePr) {
            return $this->redirect()->toRoute('access_denied');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

            'redirectUrl' => $redirectUrl,
            'entity' => $entity,
            'errors' => null,
            'n' => 0,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("procure/pr/crud");
        return $viewModel;
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

    /**
     *
     * @return \Procure\Service\PrService
     */
    public function getPrService()
    {
        return $this->prService;
    }

    /**
     *
     * @param \Procure\Service\PrService $prService
     */
    public function setPrService(\Procure\Service\PrService $prService)
    {
        $this->prService = $prService;
    }
    
    
  
}
