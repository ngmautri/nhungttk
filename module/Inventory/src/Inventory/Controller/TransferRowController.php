<?php
namespace Inventory\Controller;

use Application\Entity\NmtInventoryMv;
use Application\Entity\NmtInventoryTrx;
use Application\Entity\NmtProcurePoRow;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Zend\Escaper\Escaper;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtInventoryTransfer;

/**
 * Goods Transfer Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransferRowController extends AbstractActionController
{

    protected $doctrineEM;

    protected $inventoryTransactionService;

    protected $itemReportService;

    /**
     *
     * @return \Inventory\Service\Report\ItemReportService
     */
    public function getItemReportService()
    {
        return $this->itemReportService;
    }

    /**
     *
     * @param \Inventory\Service\Report\ItemReportService $itemReportService
     */
    public function setItemReportService(\Inventory\Service\Report\ItemReportService $itemReportService)
    {
        $this->itemReportService = $itemReportService;
    }

    /**
     *
     * @return \Inventory\Service\InventoryTransactionService
     */
    public function getInventoryTransactionService()
    {
        return $this->inventoryTransactionService;
    }

    /**
     *
     * @param \Inventory\Service\InventoryTransactionService $inventoryTransactionService
     */
    public function setInventoryTransactionService(\Inventory\Service\InventoryTransactionService $inventoryTransactionService)
    {
        $this->inventoryTransactionService = $inventoryTransactionService;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $this->layout("Inventory/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $issueType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());

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

            $target_id = $data['target_id'];
            $target_token = $data['target_token'];

            /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
            $mv = $res->getMovement($target_id, $target_token);

            if ($mv == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            $target = null;
            if ($mv[0] instanceof NmtInventoryMv) {
                /**@var \Application\Entity\NmtInventoryMv $target ;*/
                $target = $mv[0];
            }

            if ($target == null) {

                $errors[] = 'Inventory Movement object can\'t be empty. Or token key is not valid!';

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'form_action' => '/inventory/transfer-row/add',
                    'form_title' => $nmtPlugin->translate("New Goods Transfer Row"),                    
                    
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null,
                    'issueType' => $issueType,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("inventory/transfer-row/crud" . $target->getMovementType());
                return $viewModel;
            }

            $entity = new NmtInventoryTrx();
            $errors = $this->inventoryTransactionService->saveRow($target, $entity, $data, $u, true, False, __METHOD__);

            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'form_action' => '/inventory/transfer-row/add',
                    'form_title' => $nmtPlugin->translate("New Goods Transfer Line"),
                    
                    
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'issueType' => $issueType,

                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("inventory/transfer-row/crud" . $target->getMovementType());
                return $viewModel;
            }

            $redirectUrl = "/inventory/transfer-row/add?token=" . $target->getToken() . "&target_id=" . $target->getId();
            $m = sprintf("[OK] GR Line: %s created!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate.....................
        // ==============================

        $redirectUrl = Null;

        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('access_denied');
         * }
         *
         * $redirectUrl = $this->getRequest()
         * ->getHeader('Referer')
         * ->getUri();
         */

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
        $gi = $res->getMovement($id, $token);

        if ($gi == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $target = null;
        if ($gi[0] instanceof NmtInventoryMv) {
            $target = $gi[0];
        }

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        /**@var \Application\Entity\NmtInventoryMv $target ;*/

        $entity = new NmtInventoryTrx();

        // set null
        $entity->setIsActive(1);
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'form_action' => '/inventory/transfer-row/add',
            'form_title' => $nmtPlugin->translate("New Goods Transfer Line"),
            
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'issueType' => $issueType,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("inventory/transfer-row/crud" . $target->getMovementType()); // important
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
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $this->layout("Inventory/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $issueType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());

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
            $entity_id = $data['entity_id'];
            $entity_token = $data['token'];
            $nTry = $data['n'];

            $criteria = array(
                'id' => $entity_id,
                'token' => $entity_token
            );

            /** @var \Application\Entity\NmtInventoryTrx $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'nmtPlugin' => $nmtPlugin,
                    'n' => 0
                ));
            }
            $target = $entity->getMovement();

            $errors = $this->inventoryTransactionService->saveRow($entity->getMovement(), $entity, $data, $u, false, False, __METHOD__);

            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit "Row. %s"?', $entity->getId());
            }

            if ($nTry == 5) {
                $m = sprintf('You might be not ready to edit AP Row (%s). Please try later!', $entity->getId);
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
                    'issueType' => $issueType,
                    'n' => 0,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
                return $viewModel;
            }

            $m = sprintf('[OK] WH transaction GI line #%s - %s  updated.', $entity->getId(), $entity->getId());

            $this->flashMessenger()->addMessage($m);
            $redirectUrl = "/inventory/gi/review?token=" . $target->getToken() . "&entity_id=" . $target->getId();

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate.....................
        // ==============================

        $redirectUrl = Null;

        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('access_denied');
         * }
         *
         * $redirectUrl = $this->getRequest()
         * ->getHeader('Referer')
         * ->getUri();
         */

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /** @var \Application\Entity\NmtInventoryTrx $entity ; */

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
        if ($entity == null) {
            // return $this->redirect()->toRoute('access_denied');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $entity->getMovement(),
            'issueType' => $issueType,
            'nmtPlugin' => $nmtPlugin,
            'n' => 0
        ));

        $viewModel->setTemplate("inventory/gi-row/add" . $entity->getMovement()
            ->getMovementType());
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function girdAction()
    {
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        }

        // $pq_curPage = $_GET ["pq_curpage"];
        // $pq_rPP = $_GET ["pq_rpp"];

        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryMv')->findOneBy($criteria);

        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();

        if ($target instanceof \Application\Entity\NmtInventoryMv) {

            $query = 'SELECT e FROM Application\Entity\NmtInventoryTrx e
            WHERE e.movement=?1';

            $list = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => $target
            ))
                ->getResult();

            $total_records = 0;
            if (count($list) > 0) {
                $escaper = new Escaper();

                $total_records = count($list);
                foreach ($list as $a) {

                    /** @var \Application\Entity\NmtInventoryTrx $a ;*/

                    $a_json_row["row_id"] = $a->getId();
                    $a_json_row["row_token"] = $a->getToken();
                    $a_json_row["row_quantity"] = $a->getQuantity();

                    $item_detail = "/inventory/item/show1?token=" . $a->getItem()->getToken() . "&checksum=" . $a->getItem()->getChecksum() . "&entity_id=" . $a->getItem()->getId();
                    if ($a->getItem()->getItemName() !== null) {
                        $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($a->getItem()
                            ->getItemName()) . "','1450',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showJqueryDialog('Detail of Item: " . ($a->getItem()->getItemName()) . "','1450',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    }

                    if (strlen($a->getItem()->getItemName()) < 35) {
                        $a_json_row["item_name"] = $a->getItem()->getItemName() . '<a style="cursor:pointer;color:blue"  item-pic="" id="' . $a->getItem()->getId() . '" item_name="' . $a->getItem()->getItemName() . '" title="' . $a->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                    } else {
                        $a_json_row["item_name"] = substr($a->getItem()->getItemName(), 0, 30) . '<a style="cursor:pointer;;color:blue"  item-pic="" id="' . $a->getItem()->getId() . '" item_name="' . $a->getItem()->getItemName() . '" title="' . $a->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                    }

                    // $a_json_row["item_name"] = $a->getItem()->getItemName();

                    $a_json_row["item_sku"] = $a->getItem()->getItemSku();
                    $a_json_row["item_token"] = $a->getItem()->getToken();
                    $a_json_row["item_checksum"] = $a->getItem()->getChecksum();

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
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateRowAction()
    {
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

            /** @var \Application\Entity\NmtProcureGrRow $entity */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->findOneBy($criteria);

            if ($entity != null) {
                $entity->setQuantity($a['row_quantity']);
                $entity->setFaRemarks($a['fa_remarks']);
                $entity->setRowNumber($a['row_number']);
                // $a_json_final['updateList']=$a['row_id'] . 'has been updated';
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
  
}
