<?php
namespace Procure\Controller;

use Zend\Escaper\Escaper;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\NmtProcurePo;
use Application\Entity\NmtProcurePoRow;
use Application\Entity\NmtInventoryTrx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Procure\Application\Reporting\PO\Output\PoRowStatusOutputStrategy;
use Procure\Application\Reporting\PO\PoReporter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowController extends AbstractActionController
{

    protected $doctrineEM;

    protected $poService;

    protected $poSearchService;
    
    /**
     *
     * @var PoReporter $poReporter ;
     */
    protected $poReporter;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function statusReportAction()
    {

        // $this->layout ( "layout/fluid" );
        $item_type = $this->params()->fromQuery('item_type');
        $is_active = (int) $this->params()->fromQuery('is_active');
        $is_fixed_asset = (int) $this->params()->fromQuery('is_fixed_asset');

        $output = (int) $this->params()->fromQuery('output');

        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $po_year = $this->params()->fromQuery('po_year');

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 30;
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

        if ($output == null) :
            // $sort_by = "prNumber";
            $output = PoRowStatusOutputStrategy::OUTPUT_IN_ARRAY;
        endif;

        if ($sort_by == null) :
            // $sort_by = "prNumber";
            $sort_by = "vendorName";
        endif;

            // $n = new NmtInventoryItem();
        if ($balance == null) :
            $balance = 1;
        endif;

        if ($is_active == null) :
            $is_active = 1;
        endif;

            // $n = new NmtInventoryItem();
        if ($po_year == null) :
             $po_year = date('Y');
        endif;

        if ($sort == null) :
            $sort = "ASC";
        endif;

        $paginator = null;
        $result = null;

        $total_records = count($this->getPoReporter()->getAllPoRowStatus($is_active, $po_year, $balance, $sort_by, $sort, 0, 0, $output));

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $result = $this->getPoReporter()->getAllPoRowStatus($is_active, $po_year, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1, $output);
        } else {
            $result = $this->getPoReporter()->getAllPoRowStatus($is_active, $po_year, $balance, $sort_by, $sort, 0, 0, $output);
        }

        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));

        return new ViewModel(array(
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'is_fixed_asset' => $is_fixed_asset,
            'per_pape' => $resultsPerPage,
            'item_type' => $item_type,
            'balance' => $balance,
            'po_year' => $po_year,
            'output' => $output,
            'result' => $result,
            'paginator' => $paginator
        ));
    }
    
    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function statusReportGirdAction()
    {
        if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
        } else {
            $sort_by = "itemName";
        }
        
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        } else {
            $sort = "ASC";
        }
        
        if (isset($_GET['balance'])) {
            $balance = $_GET['balance'];
        } else {
            $balance = 1;
        }
        
        if (isset($_GET['is_active'])) {
            $is_active = (int) $_GET['is_active'];
        } else {
            $is_active = 1;
        }
        
        if (isset($_GET['po_year'])) {
            
            $po_year = $_GET['po_year'];
        } else {
            $po_year = date('Y');
        }
        
        if (isset($_GET["pq_curpage"])) {
            $pq_curPage = $_GET["pq_curpage"];
        } else {
            $pq_curPage = 1;
        }
        
        if (isset($_GET["pq_rpp"])) {
            $pq_rPP = $_GET["pq_rpp"];
        } else {
            $pq_rPP = 1;
        }
        $output = PoRowStatusOutputStrategy::OUTPUT_IN_ARRAY;
        $total_records = count($this->getPoReporter()->getAllPoRowStatus($is_active, $po_year, $balance, $sort_by, $sort, 0, 0, $output));
        
        $a_json_final = array();
        
        if ($total_records > 0) {
            
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $result = $this->getPoReporter()->getAllPoRowStatus($is_active, $po_year, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1, $output);
            } else {
                $result = $this->getPoReporter()->getAllPoRowStatus($is_active, $po_year, $balance, $sort_by, $sort, 0, 0, $output);
            }
            
            $a_json_final['data'] = $result;
            $a_json_final['totalRecords'] = $total_records;
            $a_json_final['curPage'] = $pq_curPage;
        }
        
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        $request = $this->getRequest();

        // Is Posting .................
        // ============================
        if ($request->isPost()) {
            $errors = array();
            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $po_id = (int) $data['target_id'];
            $po_token = $data['target_token'];

            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po = $res->getPo($po_id, $po_token);

            if ($po == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            $target = null;
            if ($po[0] instanceof NmtProcurePo) {

                /**@var \Application\Entity\NmtProcurePo $target ;*/
                $target = $po[0];
            }

            if ($target == null) {

                $errors[] = 'Contract /PO object can\'t be empty. Or token key is not valid!';
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'total_row' => $po['total_row'],
                    'max_row_number' => $po['total_row'],
                    'net_amount' => $po['net_amount'],
                    'tax_amount' => $po['tax_amount'],
                    'gross_amount' => $po['gross_amount']
                ));

                $viewModel->setTemplate("procure/po-row/add-row");
                return $viewModel;
            }

            $entity = new NmtProcurePoRow();
            $entity->setPo($target);

            try {
                $errors = $this->poService->validateRow($target, $entity, $data);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'total_row' => $po['total_row'],
                    'max_row_number' => $po['total_row'],
                    'net_amount' => $po['net_amount'],
                    'tax_amount' => $po['tax_amount'],
                    'gross_amount' => $po['gross_amount']
                ));

                $viewModel->setTemplate("procure/po-row/add-row");
                return $viewModel;
            }
            ;

            // No ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            try {
                $this->poService->saveRow($target, $entity, $u, TRUE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            // second check.

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'total_row' => $po['total_row'],
                    'max_row_number' => $po['total_row'],
                    'net_amount' => $po['net_amount'],
                    'tax_amount' => $po['tax_amount'],
                    'gross_amount' => $po['gross_amount']
                ));

                $viewModel->setTemplate("procure/po-row/add-row");
                return $viewModel;
            }

            $redirectUrl = "/procure/po-row/add?token=" . $target->getToken() . "&target_id=" . $target->getId();
            $m = sprintf("[OK] Contract /PO Line: %s created!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = Null;

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($id, $token);

        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $target = null;
        if ($po[0] instanceof NmtProcurePo) {
            $target = $po[0];
        }

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = new NmtProcurePoRow();

        // set null
        $entity->setIsActive(1);
        $entity->setConversionFactor(1);
        $entity->setUnit("each");
        $entity->setTaxRate(0);

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list,
            'total_row' => $po['total_row'],
            'max_row_number' => $po['total_row'],
            'net_amount' => $po['net_amount'],
            'tax_amount' => $po['tax_amount'],
            'gross_amount' => $po['gross_amount']
        ));

        $viewModel->setTemplate("procure/po-row/add-row");
        return $viewModel;
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
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        if ($entity !== null) {
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
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function processAction()
    {
        $request = $this->getRequest();

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

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        if ($entity !== null) {
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
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();

        $request = $this->getRequest();

        // Is Posing
        // =============================

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

            /** @var \Application\Entity\NmtProcurePoRow $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'total_row' => null,
                    'max_row_number' => null,
                    'net_amount' => null,
                    'tax_amount' => null,
                    'gross_amount' => null,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("procure/po-row/add-row");
                return $viewModel;
            }

            // entity found
            $target = $entity->getPo();

            if ($target == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po = $res->getPo($target->getId(), $target->getToken());

            if ($po == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            // target ok

            $oldEntity = clone ($entity);

            try {
                $errors = $this->poService->validateRow($target, $entity, $data);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            /**
             *
             * @todo: problem when both attribut is 0
             */
            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

            if (count($changeArray) == 0) {
                $nTry ++;
                $errors[] = sprintf('Nothing changed! n = %s', $nTry);
            }

            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit "AP Row. %s"?', $entity->getRowIdentifer());
            }

            if ($nTry == 5) {
                $m = sprintf('You might be not ready to edit AP Row (%s). Please try later!', $entity->getRowIdentifer());
                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }

            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'total_row' => $po['total_row'],
                    'max_row_number' => $po['total_row'],
                    'net_amount' => $po['net_amount'],
                    'tax_amount' => $po['tax_amount'],
                    'gross_amount' => $po['gross_amount'],
                    'n' => $nTry
                ));

                $viewModel->setTemplate("procure/po-row/add-row");
                return $viewModel;
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $changeOn = new \DateTime();

            try {
                $this->poService->saveRow($target, $entity, $u, FALSE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'total_row' => $po['total_row'],
                    'max_row_number' => $po['total_row'],
                    'net_amount' => $po['net_amount'],
                    'tax_amount' => $po['tax_amount'],
                    'gross_amount' => $po['gross_amount'],
                    'n' => $nTry
                ));

                $viewModel->setTemplate("procure/po-row/add-row");
                return $viewModel;
            }

            $m = sprintf('[OK] PO Row #%s - %s  updated. Change No.=%s.', $entity->getId(), $entity->getRowIdentifer(), count($changeArray));

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
                'changeValidFrom' => $changeOn
            ));

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));

            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/procure/po/review?token=" . $target->getToken() . "&entity_id=" . $target->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /** @var \Application\Entity\NmtProcurePoRow $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findOneBy($criteria);
        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $target = $entity->getPo();

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($target->getId(), $target->getToken());

        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list,
            'total_row' => $po['total_row'],
            'max_row_number' => $po['total_row'],
            'net_amount' => $po['net_amount'],
            'tax_amount' => $po['tax_amount'],
            'gross_amount' => $po['gross_amount'],
            'n' => 0
        ));

        $viewModel->setTemplate("procure/po-row/add-row");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function girdAction()
    {
        $request = $this->getRequest();

        // $pq_curPage = $_GET ["pq_curpage"];
        // $pq_rPP = $_GET ["pq_rpp"];

        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );

        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo')->findOneBy($criteria);

        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();

        if ($target instanceof \Application\Entity\NmtProcurePo) {

            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $list = $res->getPOStatus($target_id, $token);

            $decimalNo = 0;

            $currency_decimal = array(
                "USD",
                "THB",
                "EUR"
            );

            if (in_array($target->getCurrency()->getCurrency(), $currency_decimal)) {
                $decimalNo = 2;
            }

            $total_records = 0;
            if (count($list) > 0) {
                $escaper = new Escaper();

                $total_records = count($list);
                foreach ($list as $r) {

                    /** @var \Application\Entity\NmtProcurePoRow $a ;*/
                    $a = $r[0];

                    // $a_json_row["row_identifer"] = sprintf('<span style="font-size:8pt; color: graytext">%s</span>',$a->getRowIdentifer());

                    // $a_json_row["row_identifer"] = '<span style="font-size:8pt; color: graytext">'. $a->getRowIdentifer() .'</span>' ;

                    $a_json_row["row_identifer"] = $a->getRowIdentifer();

                    $a_json_row["row_id"] = $a->getId();
                    $a_json_row["row_token"] = $a->getToken();
                    $a_json_row["row_number"] = $a->getRowNumber();
                    $a_json_row["row_unit"] = $a->getUnit();
                    $a_json_row["row_quantity"] = $a->getQuantity();

                    if ($a->getUnitPrice() !== null) {
                        $a_json_row["row_unit_price"] = number_format($a->getUnitPrice(), $decimalNo);
                    } else {
                        $a_json_row["row_unit_price"] = 0;
                    }

                    if ($a->getNetAmount() !== null) {
                        $a_json_row["row_net"] = number_format($a->getNetAmount(), $decimalNo);
                    } else {
                        $a_json_row["row_net"] = 0;
                    }

                    if ($a->getTaxRate() !== null) {
                        $a_json_row["row_tax_rate"] = $a->getTaxRate();
                    } else {
                        $a_json_row["row_tax_rate"] = 0;
                    }

                    if ($a->getGrossAmount() !== null) {
                        $a_json_row["row_gross"] = number_format($a->getGrossAmount(), $decimalNo);
                    } else {
                        $a_json_row["row_gross"] = 0;
                    }

                    $a_json_row["pr_number"] = "";
                    if ($a->getPrRow() !== null) {
                        if ($a->getPrRow()->getPr() !== null) {

                            $link = sprintf('<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/show?token=%s&entity_id=%s&checkum=%s">&nbsp;&nbsp;(i)&nbsp;</a>', $a->getPrRow()->getRowIdentifer(), $a->getPrRow()
                                ->getPr()
                                ->getToken(), $a->getPrRow()
                                ->getPr()
                                ->getId(), $a->getPrRow()
                                ->getPr()
                                ->getChecksum());

                            $a_json_row["pr_number"] = '<span style="font-size:8pt; color: graytext">' . $a->getPrRow()
                                ->getPr()
                                ->getPrName() . '</span>' . $link;
                        }
                    }

                    if ($r['draft_gr_qty'] > 0) {
                        $a_json_row["draft_gr"] = number_format($r['draft_gr_qty'], 2);
                    } else {
                        $a_json_row["draft_gr"] = 0;
                    }

                    $url = sprintf("/procure/po-row/gr-of?token=%s&entity_id=%s", $a->getToken(), $a->getId());
                    $onclick1 = sprintf("showJqueryDialog('Goods Receipt ','1350',$(window).height()-50,'%s','j_loaded_data', true);", $url);
                    $received_detail = sprintf('<a title="click for goods receipt!" style="color: #337ab7;" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', $onclick1);

                    if ($r['posted_gr_qty'] > 0) {
                        $a_json_row["confirmed_gr"] = number_format($r['posted_gr_qty'], 2) . $received_detail;
                    } else {
                        $a_json_row["confirmed_gr"] = 0;
                    }

                    if ($r['open_gr_qty'] > 0) {
                        $a_json_row["open_gr"] = number_format($r['open_gr_qty'], 2);
                    } else {
                        $a_json_row["open_gr"] = 0;
                    }

                    $item_detail = sprintf("/inventory/item/show1?token=%s&checksum=%s&entity_id=%s", $a->getItem()->getToken(), $a->getItem()->getChecksum(), $a->getItem()->getId());

                    if ($a->getItem()->getItemName() !== null) {
                        $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($a->getItem()
                            ->getItemName()) . "','1500',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showJqueryDialog('Detail of Item: " . ($a->getItem()->getItemName()) . "','1500',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    }

                    if (strlen($a->getItem()->getItemName()) < 35) {
                        $a_json_row["item_name"] = $a->getItem()->getItemName() . '<a style="cursor:pointer;color:#337ab7"  item-pic="" id="' . $a->getItem()->getId() . '" item_name="' . $a->getItem()->getItemName() . '" title="' . $a->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;(i)&nbsp;</a>';
                    } else {
                        $a_json_row["item_name"] = substr($a->getItem()->getItemName(), 0, 30) . '<a style="cursor:pointer;color:#337ab7"  item-pic="" id="' . $a->getItem()->getId() . '" item_name="' . $a->getItem()->getItemName() . '" title="' . $a->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;(i)&nbsp;</a>';
                    }

                    // $a_json_row["item_name"] = $a->getItem()->getItemName();

                    $a_json_row["row_name"] = '<span style="font-size:8pt; color: graytext">' . $a->getVendorItemName() . '</span>';
                    $a_json_row["row_code"] = '<span style="font-size:8pt; color: graytext">' . $a->getVendorItemCode() . '</span>';

                    $a_json_row["item_sku"] = sprintf('<span style="font-size:9pt; color: black">%s</span>', $a->getItem()->getItemSku());

                    $a_json_row["item_token"] = $a->getItem()->getToken();
                    $a_json_row["item_checksum"] = $a->getItem()->getChecksum();
                    $a_json_row["fa_remarks"] = $a->getFaRemarks();
                    $a_json_row["remarks"] = $a->getRemarks();

                    if ($r['billed_amount'] > 0) {
                        $a_json_row["billed_amount"] = number_format($r['billed_amount'], $decimalNo);
                    } else {
                        $a_json_row["billed_amount"] = 0;
                    }

                    $a_json_row["exw_unit_price"] = $a->getExwUnitPrice();
                    $a_json_row["total_exw_price"] = $a->getTotalExwPrice();

                    $standard_qty = $a->getQuantity();

                    if ($a->getConvertedStandardQuantity() != null) {
                        $standard_qty = $a->getConvertedStandardQuantity();
                    }

                    if ($standard_qty != null) {
                        $standard_qty = number_format($standard_qty, 2);
                    }
                    $a_json_row["standard_qty"] = $standard_qty;

                    $standard_unit = $a->getUnit();

                    if ($a->getItem() !== null) {
                        if ($a->getItem()->getStandardUom() !== null) {
                            $standard_unit = $a->getItem()
                                ->getStandardUom()
                                ->getUomCode();
                        }
                    }

                    $a_json_row["standard_unit"] = $standard_unit;

                    $a_json_row["doc_qty"] = $a->getDocQuantity();

                    if ($a->getDocUnitPrice() !== null) {
                        $a_json_row["doc_unit_price"] = number_format($a->getDocUnitPrice(), $decimalNo);
                    } else {
                        $a_json_row["doc_unit_price"] = 0;
                    }

                    $a_json[] = $a_json_row;
                }
            }

            $a_json_final['data'] = $a_json;
            $a_json_final['totalRecords'] = $total_records;
            // $a_json_final ['curPage'] = $pq_curPage;
        }

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /**
     *
     * @return \Zend\Http\Response
     */
    public function downloadAction()
    {
        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $rows = $res->downLoadVendorPo($target_id, $token);

        if ($rows != + null) {

            $target = null;
            if (count($rows) > 0) {
                $pr_row_1 = $rows[0];
                if ($pr_row_1 instanceof NmtProcurePoRow) {
                    $target = $pr_row_1->getPo();
                }

                // Create new PHPExcel object
                $objPHPExcel = new Spreadsheet();

                // Set document properties
                $objPHPExcel->getProperties()
                    ->setCreator("Nguyen Mau Tri")
                    ->setLastModifiedBy("Nguyen Mau Tri")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");

                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $target->getInvoiceNo());

                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $target->getInvoiceDate());

                $header = 3;
                $i = 0;

                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "Contract/PO:" . $target->getSysNumber());

                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $header, "FA Remarks");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $header, "#");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $header, "SKU");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $header, "Item");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $header, "Unit");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $header, "Quantity");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $header, "Unit Price");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $header, "Net Amount");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $header, "Tax Rate");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $header, "Tax Amount");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $header, "Gross Amount");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $header, "PR Number");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $header, "PR Date");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $header, "Requested Q/ty");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $header, "Requested Name");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $header, "RowNo.");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $header, "Remarks");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $header, "Ref.No.");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $header, "Item.No.");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $header, "Po.Item Name");

                foreach ($rows as $r) {

                    /**@var \Application\Entity\NmtProcurePoRow $a ;*/
                    $a = $r;

                    $i ++;
                    $l = $header + $i;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $l, $a->getFaRemarks());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $l, $i);

                    if ($a->getItem() !== null) {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, $a->getItem()
                            ->getItemSku());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, $a->getItem()
                            ->getItemName());
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, "NA");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, "NA");
                    }
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, $a->getUnit());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, $a->getQuantity());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $l, $a->getUnitPrice());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, $a->getNetAmount());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $l, $a->getTaxRate());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $l, $a->getTaxAmount());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $l, $a->getGrossAmount());

                    if ($a->getPrRow() !== null) {

                        if ($a->getPrRow()->getPr() !== null) {
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $l, $a->getPrRow()
                                ->getPr()
                                ->getPrNumber());
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $l, $a->getPrRow()
                                ->getPr()
                                ->getSubmittedOn());
                        }
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $l, $a->getPrRow()
                            ->getQuantity());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $l, $a->getPrRow()
                            ->getRowName());
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $l, "NA");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $l, "NA");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $l, 0);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $l, "");
                    }

                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $l, $a->getRowNumber());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $l, $a->getRemarks());

                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $l, "-");
                    if ($a->getPrRow() !== null) {

                        if ($a->getPrRow()->getPr() !== null) {
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $l, $a->getPrRow()
                                ->getPr()
                                ->getPrNumber());
                        }
                    }

                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $l, $a->getItem()
                        ->getSysNumber());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $l, $a->getVendorItemCode());
                }

                // Rename worksheet
                $objPHPExcel->getActiveSheet()->setTitle("Contract-PO");

                $objPHPExcel->getActiveSheet()->setAutoFilter("A" . $header . ":T" . $header);

                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $objPHPExcel->setActiveSheetIndex(0);

                // Redirect output to a client’s web browser (Excel2007)
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="invoice' . $target->getId() . '.xlsx"');
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');

                // If you're serving to IE over SSL, then the following may be needed
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
                header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header('Pragma: public'); // HTTP/1.0

                $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
                $objWriter->save('php://output');
                exit();
            }
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function listAction()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ;

        $this->layout("layout/user/ajax");

        $invoice_id = (int) $this->params()->fromQuery('target_id');
        $invoice_token = $this->params()->fromQuery('token');

        $criteria = array(
            'id' => $invoice_id,
            'token' => $invoice_token
        );

        /** @var \Application\Entity\FinVendorInvoice $target ;*/
        $target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);

        if ($target !== null) {

            $criteria = array(
                'invoice' => $invoice_id,
                'isActive' => 1
            );

            $query = 'SELECT e FROM Application\Entity\FinVendorInvoiceRow e
            WHERE e.invoice=?1 AND e.isActive =?2';

            $list = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => $target,
                "2" => 1
            ))
                ->getResult();
            return new ViewModel(array(
                'list' => $list,
                'total_records' => count($list),
                'paginator' => null
            ));
        }

        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function rowOfVendorAction()
    {
        $request = $this->getRequest();

        // accepted only ajax request

        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        $vendor_id = (int) $this->params()->fromQuery('target_id');
        $vendor_token = $this->params()->fromQuery('token');

        $is_active = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');

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

        $is_active = (int) $this->params()->fromQuery('is_active');

        if ($is_active == null) {
            $is_active = 1;
        }

        if ($sort_by == null) :
            $sort_by = "itemName";
        endif;

        if ($sort == null) :
            $sort = "ASC";
        endif;

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePo');
        $list = $res->getPoRowOfVendor($vendor_id, $vendor_token, $sort_by, $sort);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getPoRowOfVendor($vendor_id, $vendor_token, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState,
            'vendor_id' => $vendor_id,
            'vendor_token' => $vendor_token
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function poOfItemAction()
    {
        $request = $this->getRequest();
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        $this->layout("layout/user/ajax");

        $item_id = (int) $this->params()->fromQuery('item_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $rows = $res->getPoOfItem($item_id, $token);
        return new ViewModel(array(
            'rows' => $rows
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function grOfAction()
    {
        $request = $this->getRequest();
        // accepted only ajax request
        /*
         * if (! $request->isXmlHttpRequest()) {
         * return $this->redirect()->toRoute('access_denied');
         * }
         */
        $this->layout("layout/user/ajax");

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $rows = $res->getGrOfPoRow($id, $token);

        return new ViewModel(array(
            'rows' => $rows
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateRowAction()
    {
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $a_json_final = array();
        $escaper = new Escaper();

        /*
         * $pq_curPage = $_GET ["pq_curpage"];
         * $pq_rPP = $_GET ["pq_rpp"];
         */
        $sent_list = json_decode($_POST['sent_list'], true);
        // echo json_encode($sent_list);

        $to_update = $sent_list['updateList'];

        foreach ($to_update as $a) {
            $criteria = array(
                'id' => $a['row_id'],
                'token' => $a['row_token']
            );

            /** @var \Application\Entity\NmtProcurePoRow $entity */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->findOneBy($criteria);

            if ($entity != null) {

                $errors = $this->poService->validateRow1($entity, $a);
                if (count($errors) > 0) {
                    $response = $this->getResponse();
                    $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');

                    $a_json_final['status'] = \Application\Model\Constants::AJAX_FAILED;
                    $a_json_final['message'] = $errors;
                    $response->setContent(json_encode($a_json_final));
                    return $response;
                }

                $this->poService->saveRow($entity->getPo(), $entity, $u);
                $this->doctrineEM->persist($entity);
            }
        }
        $this->doctrineEM->flush();

        // $a_json_final["updateList"]= json_encode($sent_list["updateList"]);

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($sent_list));
        return $response;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();
        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        return new ViewModel(array(
            'list' => $list
        ));
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
     * @return \Procure\Service\PoService
     */
    public function getPoService()
    {
        return $this->poService;
    }

    /**
     *
     * @param \Procure\Service\PoService $poService
     */
    public function setPoService(\Procure\Service\PoService $poService)
    {
        $this->poService = $poService;
    }

    /**
     *
     * @return \Procure\Service\PoService
     */
    public function getPoSearchService()
    {
        return $this->poSearchService;
    }

    /**
     *
     * @param \Procure\Service\PoService $poSearchService
     */
    public function setPoSearchService(\Procure\Service\PoSearchService $poSearchService)
    {
        $this->poSearchService = $poSearchService;
    }
    /**
     * @return \Procure\Application\Reporting\PO\PoReporter
     */
    public function getPoReporter()
    {
        return $this->poReporter;
    }

    /**
     * @param \Procure\Application\Reporting\PO\PoReporter $poReporter
     */
    public function setPoReporter(PoReporter $poReporter)
    {
        $this->poReporter = $poReporter;
    }

}
