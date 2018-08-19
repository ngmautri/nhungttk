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

/**
 * Good Receipt PO or PR or AP
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIRowController extends AbstractActionController
{

    protected $doctrineEM;

    protected $giService;

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
        $currency_list = $nmtPlugin->currencyList();
        $issueType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());
        $gl_list = $nmtPlugin->glAccountList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        // Is Posting .................
        // ============================
        if ($request->isPost()) {
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

            $mv_id = $request->getPost('mv_id');
            $mv_token = $request->getPost('mv_token');

            /**@var \Application\Repository\NmtInventoryItemRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
            $mv = $res->getMovement($mv_id, $mv_token);

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
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null,
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'issueType' => $issueType
                ));

                $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
                return $viewModel;
            }
            
            $item_id = $request->getPost('item_id');
            $issue_for_id = $request->getPost('issue_for_id');
            $isActive = (int) $request->getPost('isActive');
            $quantity = $request->getPost('quantity');
            $project_id = (int) $request->getPost('project_id');
            $cost_center_id = (int) $request->getPost('cost_center_id');

            $remarks = $request->getPost('remarks');

            if ($isActive !== 1) {
                $isActive = 0;
            }

            // Inventory Transaction:
            $entity = new NmtInventoryTrx();

            $entity->setFlow($target->getMovementFlow());
            $entity->setWh($target->getWarehouse());
            $entity->setIsActive($isActive);
            $entity->setMovement($target);

            /**@var \Application\Entity\NmtInventoryItem $item ;*/
            $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);

            $for_item = null;
            if ($issue_for_id > 0) {
                $for_item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($issue_for_id);
                $entity->setIssueFor($for_item);
            }

            $project = null;
            if ($project_id > 0) {
                $project = $this->doctrineEM->getRepository('Application\Entity\NmtPmProject')->find($project_id);
                $entity->setProject($project);
            }

            $costCenter = null;
            if ($cost_center_id > 0) {
                $costCenter = $this->doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($cost_center_id);
                $entity->setCostCenter($costCenter);
            }

            if ($item == null) {
                $errors[] = $nmtPlugin->translate('No Item is  selected');
            } else {
                if ($item->getIsStocked() != 1) {
                    $errors[] = $nmtPlugin->translate('Item is not kept in stock');
                } else {
                    $entity->setItem($item);
                }
            }

            if ($quantity == null) {
                $errors[] = 'Please  enter quantity!';
            } else {

                if (! is_numeric($quantity)) {
                    $errors[] = $nmtPlugin->translate('Quantity must be a number!');
                } else {
                    if ($quantity <= 0) {
                        $errors[] = $nmtPlugin->translate('Quantity must be greater than 0!');
                    }
                    $entity->setQuantity($quantity);
                }
            }

            // $entity->setTraceStock($traceStock);
            $entity->setRemarks($remarks);

            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'issueType' => $issueType
                ));

                $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
                return $viewModel;
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $entity->setDocStatus($target->getDocStatus());
            try {
                $this->giService->addRow($target, $entity, $u);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            // second check
            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'issueType' => $issueType
                ));

                $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
                return $viewModel;
            }

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            $redirectUrl = "/inventory/gi-row/add?token=" . $target->getToken() . "&target_id=" . $target->getId();
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
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list,
            'gl_list' => $gl_list,
            'issueType' => $issueType
        ));

        $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
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
        $currency_list = $nmtPlugin->currencyList();
        $issueType = \Inventory\Model\Constants::getGoodsIssueTypes($nmtPlugin->getTranslator());
        $gl_list = $nmtPlugin->glAccountList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        // Is Posting .................
        // ============================

        if ($request->isPost()) {

            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');

            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /** @var \Application\Entity\NmtInventoryGiRow $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryGiRow')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'currency_list' => $currency_list
                ));
            }
            
            //continue

           
            $item_id = $request->getPost('item_id');
            $issue_for_id = $request->getPost('issue_for_id');
            $isActive = (int) $request->getPost('isActive');
            $quantity = $request->getPost('quantity');
            $project_id = (int) $request->getPost('project_id');
            $cost_center_id = (int) $request->getPost('cost_center_id');
            
            $remarks = $request->getPost('remarks');
            
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            $entity->setFlow($target->getMovementFlow());
            $entity->setWh($target->getWarehouse());
            $entity->setIsActive($isActive);
            $entity->setMovement($target);
            
            /**@var \Application\Entity\NmtInventoryItem $item ;*/
            $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);
            
            $for_item = null;
            if ($issue_for_id > 0) {
                $for_item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($issue_for_id);
                $entity->setIssueFor($for_item);
            }
            
            $project = null;
            if ($project_id > 0) {
                $project = $this->doctrineEM->getRepository('Application\Entity\NmtPmProject')->find($project_id);
                $entity->setProject($project);
            }
            
            $costCenter = null;
            if ($cost_center_id > 0) {
                $costCenter = $this->doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($cost_center_id);
                $entity->setCostCenter($costCenter);
            }
            
            if ($item == null) {
                $errors[] = $nmtPlugin->translate('No Item is  selected');
            } else {
                if ($item->getIsStocked() != 1) {
                    $errors[] = $nmtPlugin->translate('Item is not kept in stock');
                } else {
                    $entity->setItem($item);
                }
            }
            
            if ($quantity == null) {
                $errors[] = 'Please  enter quantity!';
            } else {
                
                if (! is_numeric($quantity)) {
                    $errors[] = $nmtPlugin->translate('Quantity must be a number!');
                } else {
                    if ($quantity <= 0) {
                        $errors[] = $nmtPlugin->translate('Quantity must be greater than 0!');
                    }
                    $entity->setQuantity($quantity);
                }
            }
            
            // $entity->setTraceStock($traceStock);
            $entity->setRemarks($remarks);
            
            if (count($errors) > 0) {
                
                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'issueType' => $issueType
                ));
                
                $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
                return $viewModel;
            }
            
            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++
            
            $entity->setDocStatus($target->getDocStatus());
            try {
                $this->giService->addRow($target, $entity, $u);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
            
            // second check
            if (count($errors) > 0) {
                
                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'issueType' => $issueType
                ));
                
                $viewModel->setTemplate("inventory/gi-row/add" . $target->getMovementType());
                return $viewModel;
            }
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            
            $redirectUrl = "/inventory/gi-row/add?token=" . $target->getToken() . "&target_id=" . $target->getId();
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
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $entity->getMovement(),
            'currency_list' => $currency_list,
            'gl_list' => $gl_list,
            'issueType' => $issueType
        ));

        $viewModel->setTemplate("inventory/gi-row/edit" . $entity->getMovement()
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
                            ->getItemName()) . "','1310',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showJqueryDialog('Detail of Item: " . ($a->getItem()->getItemName()) . "','1310',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
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

        if ($rows !== null) {

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
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $l, $a->getRowIdentifer());
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
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function giOfItemAction()
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

    /**
     *
     * @return \Inventory\Service\GIService
     */
    public function getGiService()
    {
        return $this->giService;
    }

    /**
     *
     * @param \Inventory\Service\GIService $giService
     */
    public function setGiService(\Inventory\Service\GIService $giService)
    {
        $this->giService = $giService;
    }
}
