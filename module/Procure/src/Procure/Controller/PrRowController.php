<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Procure\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtPmProject;
use Zend\Validator\Date;
use Zend\Math\Rand;
use Application\Entity\NmtProcurePr;
use Application\Entity\NmtProcurePrRow;
use Application\Entity\NmtInventoryItem;
use Zend\Escaper\Escaper;
use PHPExcel;
use PHPExcel_IOFactory;
use Procure\Service\PrSearchService;
use Zend\Cache\Storage\StorageInterface;

/**
 *
 * @author nmt
 *        
 */
class PrRowController extends AbstractActionController
{

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    protected $prSearchService;

    protected $cacheService;

    /**
     *
     * @return \Procure\Service\PrSearchService
     */
    public function getPrSearchService()
    {
        return $this->prSearchService;
    }

    /**
     *
     * @param PrSearchService $prSearchService
     */
    public function setPrSearchService(PrSearchService $prSearchService)
    {
        $this->prSearchService = $prSearchService;
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
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findBy($criteria, $sort_criteria);
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->setChecksum(md5(uniqid("pr_row_" . $entity->getId()) . microtime()));
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
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $this->layout("Procure/layout-fullscreen");
        
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $target_id = $request->getPost('target_id');
            $token = $request->getPost('token');
            
            /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
            $pr = $res->getPR($target_id, $token);
            
            if ($pr == null) {
                return $this->redirect()->toRoute('access_denied');
            }
            
            /**@var \Application\Entity\NmtProcurePr $target ;*/
            $target = null;
            if ($pr[0] instanceof NmtProcurePr) {
                
                $target = $pr[0];
            }
            
            if ($target == null) {
                
                $errors[] = 'Target object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null,
                    'total_row' => (int) $pr[1],
                    'max_row_number' => (int) $pr[2]
                ));
                
                // might need redirect
            } else {
                
                $errors = array();
                $redirectUrl = $request->getPost('redirectUrl');
                
                $edt = $request->getPost('edt');
                $isActive = (int) $request->getPost('isActive');
                
                $rowNumber = (int) $request->getPost('rowNumber');
                
                $priority = $request->getPost('priority');
                $quantity = $request->getPost('quantity');
                
                $remarks = $request->getPost('remarks');
                $rowCode = $request->getPost('rowCode');
                $rowName = $request->getPost('rowName');
                $rowUnit = $request->getPost('rowUnit');
                $conversionFactor = $request->getPost('conversionFactor');
                
                $item_id = $request->getPost('item_id');
                $project_id = $request->getPost('project_id');
                
                if ($isActive != 1) {
                    $isActive = 0;
                }
                
                $entity = new NmtProcurePrRow();
                
                if ($item_id > 0) {
                    $item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $item_id);
                    $entity->setItem($item);
                }
                
                if ($project_id > 0) {
                    $project = $this->doctrineEM->find('Application\Entity\NmtPmProject', $project_id);
                    if ($project !== null) {
                        $entity->setProject($project);
                    }
                }
                
                $n = $pr['total_row'] + 1;
                $rowIdentifer = $target->getPrAutoNumber() . "-$n";
                
                $entity->setRowNumber($rowNumber);
                $entity->setPr($target);
                $entity->setIsActive($isActive);
                $entity->setPriority($priority);
                $entity->setRemarks($remarks);
                $entity->setRowCode($rowCode);
                $entity->setRowName($rowName);
                $entity->setRowUnit($rowUnit);
                $entity->setConversionFactor($conversionFactor);
                $entity->setRowIdentifer($rowIdentifer);
                
                if ($quantity == null) {
                    $errors[] = 'Please  enter order quantity!';
                } else {
                    
                    if (! is_numeric($quantity)) {
                        $errors[] = 'quantity must be a number.';
                    } else {
                        if ($quantity <= 0) {
                            $errors[] = 'quantity must be greater than 0!';
                        }
                        $entity->setQuantity($quantity);
                    }
                }
                
                $validator = new Date();
                
                // Empty is OK
                if ($edt !== null) {
                    if ($edt !== "") {
                        
                        if (! $validator->isValid($edt)) {
                            $errors[] = 'Date is not correct or empty!';
                        } else {
                            $entity->setEdt(new \DateTime($edt));
                        }
                    }
                }
                
                if ($conversionFactor == null) {
                    // $errors [] = 'Please enter order quantity!';
                } else {
                    
                    if (! is_numeric($conversionFactor)) {
                        $errors[] = 'quantity must be a number.';
                    } else {
                        if ($conversionFactor <= 0) {
                            $errors[] = 'quantity must be greater than 0!';
                        }
                        $entity->setConversionFactor($conversionFactor);
                    }
                }
                
                if (count($errors) > 0) {
                    return new ViewModel(array(
                        
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity,
                        'target' => $target,
                        'total_row' => $pr['total_row'],
                        'max_row_number' => $pr['max_row_number'],
                        'active_row' => $pr['active_row']
                    
                    ));
                }
                
                // NO ERROR
                // +++++++++++++++++++++++++++++++
                
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                $entity->setChecksum(md5(uniqid("pr_row_" . microtime())));
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $createdOn = new \DateTime();
                
                $entity->setCreatedBy($u);
                $entity->setCreatedOn($createdOn);
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                $m = sprintf('[OK] Row #%s for PR#%s created.', $entity->getRowIdentifer(), $target->getId());
                
                // Trigger: procure.activity.log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $createdOn
                ));
                
                $index_update_status = $this->prSearchService->updateIndex(1, $entity, FALSE);
                
                $redirectUrl = "/procure/pr-row/add?token=" . $target->getToken() . "&target_id=" . $target->getID() . "&checksum=" . $target->getChecksum();
                $this->flashMessenger()->addMessage($m);
                
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        // NO POST
        // ++++++++++++++++++++++++++++
        
        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $pr = $res->getPR($target_id, $token);
        
        if ($pr == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        /**@var \Application\Entity\NmtProcurePr $target ;*/
        $target = null;
        if ($pr[0] instanceof NmtProcurePr) {
            $target = $pr[0];
        }
        
        if ($target instanceof \Application\Entity\NmtProcurePr) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => null,
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
     * @return \Zend\View\Model\ViewModel
     */
    public function allAction()
    {
        
        // $this->layout ( "layout/fluid" );
        $item_type = $this->params()->fromQuery('item_type');
        $is_active = (int) $this->params()->fromQuery('is_active');
        $is_fixed_asset = (int) $this->params()->fromQuery('is_fixed_asset');
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $pr_year = $this->params()->fromQuery('pr_year');
        
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
        
        if ($sort_by == null) :
            // $sort_by = "prNumber";
            $sort_by = "itemName";
		endif;
        
            // $n = new NmtInventoryItem();
        if ($balance == null) :
            $balance = 1;
		endif;
        
        if ($is_active == null) :
            $is_active = 1;
		endif;
        
            // $n = new NmtInventoryItem();
        if ($pr_year == null) :
            // $pr_year = date('Y');
            $pr_year = 0;
        endif;
        
        if ($sort == null) :
            $sort = "ASC";
		endif;
        
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
            'pr_year' => $pr_year
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function all1Action()
    {
        $request = $this->getRequest();
        $redirectUrl = null;
        
        // accepted only ajax request
        /*
         * if (! $request->isXmlHttpRequest()) {
         * return $this->redirect()->toRoute('access_denied');
         * }
         */
        $this->layout("layout/user/ajax");
        
        $item_type = $this->params()->fromQuery('item_type');
        $is_active = $this->params()->fromQuery('is_active');
        $is_fixed_asset = $this->params()->fromQuery('is_fixed_asset');
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $pr_year = $this->params()->fromQuery('pr_year');
        
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
        
        // $n = new NmtInventoryItem();
        if ($sort_by == null) :
            $sort_by = "itemName";
		endif;
        
            // $n = new NmtInventoryItem();
        if ($balance == null) :
            $balance = 1;
		endif;
        
            // $n = new NmtInventoryItem();
        if ($pr_year == null) :
            // $pr_year = date('Y');
            $pr_year = 0;
		endif;
        
        if ($sort == null) :
            $sort = "ASC";
		endif;
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->getAllPrRow($pr_year, $balance, $sort_by, $sort, 0, 0);
        
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->getAllPrRow($pr_year, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }
        
        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'is_fixed_asset' => $is_fixed_asset,
            'per_pape' => $resultsPerPage,
            'item_type' => $item_type,
            'balance' => $balance,
            'pr_year' => $pr_year
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $item_type = $this->params()->fromQuery('item_type');
        $is_active = $this->params()->fromQuery('is_active');
        $is_fixed_asset = $this->params()->fromQuery('is_fixed_asset');
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        
        $criteria1 = array();
        /*
         * if (! $item_type == null) {
         * $criteria1 = array (
         * "itemType" => $item_type
         * );
         * }
         */
        $criteria2 = array();
        if (! $is_active == null) {
            $criteria2 = array(
                "isActive" => $is_active
            );
            
            if ($is_active == - 1) {
                $criteria2 = array(
                    "isActive" => '0'
                );
            }
        }
        
        $criteria3 = array();
        
        if ($sort_by == null) :
            $sort_by = "itemName";
		endif;
        
        if ($sort == null) :
            $sort = "ASC";
		endif;
        
        $sort_criteria = array(
            $sort_by => $sort
        );
        
        $criteria = array_merge($criteria1, $criteria2, $criteria3);
        
        // var_dump($criteria);
        
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
        
        $query = 'SELECT e, i, pr FROM Application\Entity\NmtProcurePrRow e JOIN e.item i JOIN e.pr pr Where 1=?1';
        
        if (! $is_active == null) {
            if ($is_active == - 1) {
                $query = $query . " AND e.isActive = 0";
            } else {
                $query = $query . " AND e.isActive = 1";
            }
        }
        
        if ($sort_by == "itemName") {
            $query = $query . ' ORDER BY i.' . $sort_by . ' ' . $sort;
        } elseif ($sort_by == "prNumber") {
            $query = $query . ' ORDER BY pr.' . $sort_by . ' ' . $sort;
        }
        $list = $this->doctrineEM->createQuery($query)
            ->setParameters(array(
            "1" => 1
        ))
            ->getResult();
        
        $total_records = count($list);
        $paginator = null;
        
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => 1
            ))
                ->setFirstResult($paginator->minInPage - 1)
                ->setMaxResults(($paginator->maxInPage - $paginator->minInPage) + 1)
                ->getResult();
        }
        
        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'is_active' => $is_active,
            'is_fixed_asset' => $is_fixed_asset,
            'per_pape' => $resultsPerPage,
            'item_type' => $item_type,
            'balance' => $balance
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ;
        
        $this->layout("layout/user/ajax");
        
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
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        
        if ($target !== null) {
            
            $criteria = array(
                'pr' => $target_id
                // 'isActive' => 1,
            );
            
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findBy($criteria);
            $total_records = count($list);
            $paginator = null;
            
            return new ViewModel(array(
                'list' => $list,
                'total_records' => $total_records,
                'paginator' => $paginator,
                'target' => $target
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function downloadAction()
    {
        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $format = (int) $this->params()->fromQuery('format');
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $rows = $res->downloadPrRows($target_id, $token);
        
        if ($rows !== null) {
            
            $target = null;
            if (count($rows) > 0) {
                $pr_row_1 = $rows[0][0];
                if ($pr_row_1 instanceof NmtProcurePrRow) {
                    $target = $pr_row_1->getPr();
                }
            }
            
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            
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
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $target->getPrName());
            
            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $target->getSubmittedOn());
            
            $header = 3;
            $i = 0;
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $header, "#");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $header, "Item");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $header, "SKU");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $header, "Quantity");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $header, "Received");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $header, "Balance");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $header, "Buying");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $header, "Item.No.");
            
            foreach ($rows as $r) {
                
                /**@var \Application\Entity\NmtProcurePrRow $a ;*/
                $a = $r[0];
                
                $i ++;
                $l = $header + $i;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $l, $i);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $l, $a->getItem()
                    ->getItemName());
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, $a->getQuantity());
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, $r['total_received']);
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, $r['confirmed_balance']);
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, $r['processing_quantity']);
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $l, $a->getItem()
                    ->getItemSku());
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, $a->getItem()
                    ->getSysNumber());
            }
            
            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle($target->getPrName());
            
            $objPHPExcel->getActiveSheet()->setAutoFilter("A3:H3");
            
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            
            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $target->getPrName() . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit();
            
            /*
             * return new ViewModel(array(
             * 'list' => $rows,
             * 'total_records' => $total_records,
             * 'target' => $target
             * ));
             */
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function printPdfAction()
    {
        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $format = (int) $this->params()->fromQuery('format');
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $rows = $res->downloadPrRows($target_id, $token);
        
        if ($rows !== null) {
            
            $target = null;
            if (count($rows) > 0) {
                $pr_row_1 = $rows[0][0];
                if ($pr_row_1 instanceof NmtProcurePrRow) {
                    $target = $pr_row_1->getPr();
                }
            }
            
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            
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
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $target->getPrName());
            
            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $target->getSubmittedOn());
            
            $header = 3;
            $i = 0;
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $header, "#");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $header, "Item");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $header, "SKU");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $header, "Quantity");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $header, "Received");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $header, "Balance");
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $header, "Buying");
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $header, "Item.No.");
            
            foreach ($rows as $r) {
                
                /**@var \Application\Entity\NmtProcurePrRow $a ;*/
                $a = $r[0];
                
                $i ++;
                $l = $header + $i;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $l, $i);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $l, $a->getItem()
                    ->getItemName());
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, $a->getQuantity());
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, $r['total_received']);
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, $r['confirmed_balance']);
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, $r['processing_quantity']);
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $l, $a->getItem()
                    ->getItemSku());
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, $a->getItem()
                    ->getSysNumber());
            }
            
            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle($target->getPrName());
            
            $objPHPExcel->getActiveSheet()->setAutoFilter("A3:H3");
            
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            
            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $target->getPrName() . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit();
            
            /*
             * return new ViewModel(array(
             * 'list' => $rows,
             * 'total_records' => $total_records,
             * 'target' => $target
             * ));
             */
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function downloadAllAction()
    {
        $is_active = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $pr_year = $this->params()->fromQuery('pr_year');
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $rows = $res->downloadAllPrRows($is_active, $pr_year, $balance, $sort_by, $sort, 0, 0);
        
        if ($rows !== null) {
            
            $target = null;
            if (count($rows) > 0) {
                $pr_row_1 = $rows[0][0];
                if ($pr_row_1 instanceof NmtProcurePrRow) {
                    $target = $pr_row_1->getPr();
                }
                
                // Create new PHPExcel object
                $objPHPExcel = new PHPExcel();
                
                // Set document properties
                $objPHPExcel->getProperties()
                    ->setCreator("Nguyen Mau Tri")
                    ->setLastModifiedBy("Nguyen Mau Tri")
                    ->setTitle("All PR Row")
                    ->setSubject("All PR Row")
                    ->setDescription("All PR Row")
                    ->setKeywords("All PR Row")
                    ->setCategory("Procurment MLA");
                
                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "All PR Row");
                
                $header = 2;
                $i = 0;
                
                // a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $header, "FA Remarks");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $header, "#");
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $header, "PR Number");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $header, "PR Date");
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $header, "SKU");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $header, "Item ID");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $header, "Item Name");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $header, "Model");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $header, "Serial");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $header, "Item Code");
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $header, "Ordered Q'ty");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $header, "Received");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $header, "Balance");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $header, "Buying");
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $header, "Last Vendor");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $header, "Last Price");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $header, "Last Curr");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $header, "Remarks");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $header, "Item No.");
                
                foreach ($rows as $r) {
                    
                    /**@var \Application\Entity\NmtProcurePrRow $a ;*/
                    $a = $r[0];
                    
                    $i ++;
                    $l = $header + $i;
                    
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $l, $a->getFaRemarks());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $l, $i);
                    
                    if ($a->getPr() !== null) {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, $a->getPr()
                            ->getPrNumber());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, $a->getPr()
                            ->getSubmittedOn());
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, " No PR No.");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, "");
                    }
                    
                    if ($a->getItem() !== null) {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, $a->getItem()
                            ->getItemSku());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, $a->getItem()
                            ->getId());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $l, $a->getItem()
                            ->getItemName());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, $a->getItem()
                            ->getManufacturerModel());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $l, $a->getItem()
                            ->getManufacturerSerial());
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $l, $a->getItem()
                            ->getManufacturerCode());
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, "");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, "");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, "");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, "");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $l, "");
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $l, "");
                    }
                    
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $l, $a->getQuantity());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $l, $r['total_received']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $l, $r['confirmed_balance']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $l, $r['processing_quantity']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $l, $r['vendor_name']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $l, $r['vendor_unit_price']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $l, $r['currency']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $l, $a->getRemarks());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $l, $a->getItem()
                        ->getSysNumber());
                }
                
                // Rename worksheet
                $objPHPExcel->getActiveSheet()->setTitle("PR Rows");
                
                $objPHPExcel->getActiveSheet()->setAutoFilter("A2:T2");
                
                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $objPHPExcel->setActiveSheetIndex(0);
                
                // Redirect output to a client’s web browser (Excel2007)
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . 'All Pr Row.xlsx"');
                header('Cache-Control: max-age=0');
                // If you're serving to IE 9, then the following may be needed
                header('Cache-Control: max-age=1');
                
                // If you're serving to IE over SSL, then the following may be needed
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
                header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                header('Pragma: public'); // HTTP/1.0
                
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
                exit();
            }
        }
        return $this->redirect()->toRoute('access_denied');
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function rowAction()
    {
        $request = $this->getRequest();
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $is_active = $this->params()->fromQuery('is_active');
        
        if ($sort_by == null) :
            $sort_by = "createdOn";
		endif;
        
        if ($balance == null) :
            $balance = 2;
		endif;
        
        if ($sort == null) :
            $sort = "ASC";
		endif;
        
        if (is_null($this->params()->fromQuery('perPage')) or $this->params()->fromQuery('perPage') == null) {
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
        
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $this->layout("layout/user/ajax");
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        
        if ($target !== null) {
            
            $criteria = array(
                'pr' => $target_id
                // 'isActive' => 1,
            );
            
            // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->get ( $criteria );
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->getPrRow($target_id, $balance, $sort_by, $sort, 0, 0);
            
            $total_records = count($list);
            $paginator = null;
            
            if ($total_records > $resultsPerPage) {
                $paginator = new Paginator($total_records, $page, $resultsPerPage);
                $list = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->getPrRow($target_id, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            }
            
            return new ViewModel(array(
                'list' => $list,
                'total_records' => $total_records,
                'paginator' => $paginator,
                'target' => $target,
                'sort_by' => $sort_by,
                'sort' => $sort,
                'per_pape' => $resultsPerPage,
                'balance' => $balance,
                'is_active' => $is_active
            
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function girdAction()
    {
        $request = $this->getRequest();
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $is_active = $this->params()->fromQuery('is_active');
        
        if ($sort_by == null) :
            $sort_by = "rowNumber";
		endif;
        
        if ($balance == null) :
            $balance = 2;
		endif;
        
        if ($sort == null) :
            $sort = "ASC";
		endif;
        
            // $pq_curPage = $_GET ["pq_curpage"];
            // $pq_rPP = $_GET ["pq_rpp"];
        
        if (is_null($this->params()->fromQuery('perPage')) or $this->params()->fromQuery('perPage') == null) {
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
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        
        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();
        
        if ($target !== null) {
            
            $criteria = array(
                'pr' => $target_id
                // 'isActive' => 1,
            );
            
            // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->get ( $criteria );
            /** @var \Application\Repository\NmtProcurePrRowRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
            
            $list = $res->getPrRow($target_id, $balance, $sort_by, $sort, 0, 0);
            $total_records = 0;
            if (count($list) > 0) {
                
                $total_records = count($list);
                
                /*
                 * if ($total_records > $pq_rPP) {
                 * $paginator = new Paginator ( $total_records, $pq_curPage, $pq_rPP );
                 * $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrRow ( $target_id, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
                 * }
                 */
                
                foreach ($list as $a) {
                    
                    $item_detail = "/inventory/item/show1?token=" . $a['item_token'] . "&checksum=" . $a['item_checksum'] . "&entity_id=" . $a['item_id'];
                    if ($a['item_name'] !== null) {
                        $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($a['item_name']) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showJqueryDialog('Detail of Item: " . ($a['item_name']) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                    }
                    
                    $a_json_row["row_id"] = $a['id'];
                    $a_json_row["row_token"] = $a['token'];
                    $a_json_row["row_checksum"] = $a['checksum'];
                    $a_json_row["row_number"] = $a['row_number'];
                    $a_json_row["row_identifer"] = $a['row_identifer'];
                    
                    $a_json_row["item_sku"] = '<span title="' . $a['item_sku'] . '">' . substr($a['item_sku'], 0, 5) . '</span>';
                    
                    if (strlen($a['item_name']) < 35) {
                        $a_json_row["item_name"] = $a['item_name'] . '<a style="cursor:pointer;color:blue"  item-pic="" id="' . $a['item_id'] . '" item_name="' . $a['item_name'] . '" title="' . $a['item_name'] . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                    } else {
                        $a_json_row["item_name"] = substr($a['item_name'], 0, 30) . '<a style="cursor:pointer;;color:blue"  item-pic="" id="' . $a['item_id'] . '" item_name="' . $a['item_name'] . '" title="' . $a['item_name'] . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                    }
                    
                    $a_json_row["quantity"] = $a['quantity'];
                    $a_json_row["confirmed_balance"] = $a['confirmed_balance'];
                    
                    $received_detail = "/inventory/item-transaction/pr-row?pr_row_id=" . $a['id'];
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . $escaper->escapeJs($a['item_name']) . "','1200',$(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
                    
                    if ($a['total_received'] > 0) {
                        $a_json_row["total_received"] = '<a style="color: #337ab7;" href="javascript:;" onclick="' . $onclick1 . '" >' . $a['total_received'] . '</a>';
                    } else {
                        $a_json_row["total_received"] = "";
                    }
                    
                    $a_json_row["confirmed_balance"] = $a['confirmed_balance'];
                    $a_json_row["buying"] = $a['processing_quantity'];
                    
                    if (strlen($a['vendor_name']) < 10) {
                        $a_json_row["vendor_name"] = $a['vendor_name'];
                    } else {
                        $a_json_row["vendor_name"] = '<span title="' . $a['vendor_name'] . '">' . substr($a['vendor_name'], 0, 8) . '...</span>';
                    }
                    
                    if ($a['vendor_unit_price'] !== null) {
                        $a_json_row["vendor_unit_price"] = number_format($a['vendor_unit_price'], 2);
                    } else {
                        $a_json_row["vendor_unit_price"] = 0;
                    }
                    
                    $a_json_row["currency"] = $a['currency'];
                    
                    $a_json_row["project_id"] = $a['project_id'];
                    
                    if (strlen($a['remarks']) < 20) {
                        $a_json_row["remarks"] = $a['remarks'];
                    } else {
                        $a_json_row["remarks"] = '<span title="' . $a['remarks'] . '">' . substr($a['remarks'], 0, 15) . '...</span>';
                    }
                    $a_json_row["fa_remarks"] = $a['fa_remarks'];
                    
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
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function gird1Action()
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
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();
        
        /** @var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        
        $list = $res->getPrRow1($target_id, $token, $is_active, $balance, $sort_by, $sort, 0, 0);
        $total_records = 0;
        
        if (count($list) > 0) {
            
            $total_records = count($list);
            
            $count = 0;
            foreach ($list as $a) {
                
                /**@var \Application\Entity\NmtProcurePrRow $pr_row_entity ;*/
                $pr_row_entity = $a[0];
                
                $item_detail = "/inventory/item/show1?token=" . $pr_row_entity->getItem()->getToken() . "&checksum=" . $pr_row_entity->getItem()->getChecksum() . "&entity_id=" . $pr_row_entity->getItem()->getId();
                if ($pr_row_entity->getItem()->getItemName() !== null) {
                    $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($pr_row_entity->getItem()
                        ->getItemName()) . "','1200',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                } else {
                    $onclick = "showJqueryDialog('Detail of Item: " . ($pr_row_entity->getItem()->getItemName()) . "','1200',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                }
                
                $count ++;
                $a_json_row["row_number"] = $pr_row_entity->getRowNumber();
                $a_json_row["row_identifer"] = $pr_row_entity->getRowIdentifer();
                
                $a_json_row["pr_number"] = $pr_row_entity->getPr()->getPrNumber() . '<a style="" target="blank"  title="' . $pr_row_entity->getPr()->getPrNumber() . '" href="/procure/pr/show?token=' . $pr_row_entity->getPr()->getToken() . '&entity_id=' . $pr_row_entity->getPr()->getId() . '&checksum=' . $pr_row_entity->getPr()->getChecksum() . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</span></a>';
                
                if ($pr_row_entity->getPr()->getSubmittedOn() !== null) {
                    $a_json_row['pr_submitted_on'] = date_format($pr_row_entity->getPr()->getSubmittedOn(), 'd-m-y');
                    // $a_json_row ['pr_submitted_on'] = $a ['submitted_on'];
                } else {
                    $a_json_row['pr_submitted_on'] = '';
                }
                
                $a_json_row["row_id"] = $pr_row_entity->getId();
                $a_json_row["row_token"] = $pr_row_entity->getToken();
                $a_json_row["row_checksum"] = $pr_row_entity->getChecksum();
                
                $a_json_row["item_sku"] = '<span title="' . $pr_row_entity->getItem()->getItemSku() . '">' . substr($pr_row_entity->getItem()->getItemSku(), 0, 5) . '</span>';
                
                if (strlen($pr_row_entity->getItem()->getItemName()) < 35) {
                    $a_json_row["item_name"] = $pr_row_entity->getItem()->getItemName() . '<a style="cursor:pointer;color:blue"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                } else {
                    $a_json_row["item_name"] = substr($pr_row_entity->getItem()->getItemName(), 0, 30) . '<a style="cursor:pointer;;color:blue"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                }
                
                $a_json_row["quantity"] = $pr_row_entity->getQuantity();
                $a_json_row["confirmed_balance"] = $a['confirmed_balance'];
                
                if (strlen($a['last_vendor_name']) < 10) {
                    $a_json_row["vendor_name"] = $a['last_vendor_name'];
                } else {
                    $a_json_row["vendor_name"] = '<span title="' . $a['last_vendor_name'] . '">' . substr($a['last_vendor_name'], 0, 8) . '...</span>';
                }
                
                if ($a['last_vendor_unit_price'] !== null) {
                    $a_json_row["vendor_unit_price"] = number_format($a['last_vendor_unit_price'], 2);
                } else {
                    $a_json_row["vendor_unit_price"] = 0;
                }
                
                $a_json_row["currency"] = $a['last_currency'];
                
                $received_detail = "/inventory/item-transaction/pr-row?pr_row_id=" . $pr_row_entity->getId();
                
                if ($pr_row_entity->getItem()->getItemName() !== null) {
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . $escaper->escapeJs($pr_row_entity->getItem()
                        ->getItemName()) . "','1200',$(window).height()-50,'" . $received_detail . "','j_loaded_data', true);";
                } else {
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . ($pr_row_entity->getItem()->getItemName()) . "','1200', $(window).height()-50,'" . $received_detail . "','j_loaded_data', true);";
                }
                
                if ($a['total_received'] > 0) {
                    $a_json_row["total_received"] = '<a style="color: #337ab7;" href="javascript:;" onclick="' . $onclick1 . '" >' . $a['total_received'] . '</a>';
                } else {
                    $a_json_row["total_received"] = "";
                }
                $a_json_row["buying"] = $a['po_quantity_draft'] + $a['po_quantity_final'] + $a['ap_quantity_draft'];
                
                if ($pr_row_entity->getProject() !== null) {
                    $a_json_row["project_id"] = $pr_row_entity->getProject()->getId();
                } else {
                    $a_json_row["project_id"] = "";
                }
                
                if (strlen($pr_row_entity->getRemarks()) < 20) {
                    $a_json_row["remarks"] = $pr_row_entity->getRemarks();
                } else {
                    $a_json_row["remarks"] = '<span title="' . $pr_row_entity->getRemarks() . '">' . substr($pr_row_entity->getRemarks(), 0, 15) . '...</span>';
                }
                $a_json_row["fa_remarks"] = $pr_row_entity->getFaRemarks();
                $a_json_row["receipt_date"] = "";
                $a_json_row["vendor"] = "";
                $a_json_row["vendor_id"] = "";
                
                $a_json[] = $a_json_row;
            }
        }
        
        $a_json_final['data'] = $a_json;
        $a_json_final['totalRecords'] = $total_records;
        // $a_json_final ['curPage'] = $pq_curPage;
        
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json_final));
        return $response;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function grGirdAction()
    {
        $request = $this->getRequest();
        
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $balance = $this->params()->fromQuery('balance');
        $is_active = $this->params()->fromQuery('is_active');
        
        if ($sort_by == null) :
            $sort_by = "createdOn";
	    endif;
        
        if ($balance == null) :
            $balance = 2;
	    endif;
        
        if ($sort == null) :
            $sort = "ASC";
	    endif;
        
            // $pq_curPage = $_GET ["pq_curpage"];
            // $pq_rPP = $_GET ["pq_rpp"];
        
        if (is_null($this->params()->fromQuery('perPage')) or $this->params()->fromQuery('perPage') == null) {
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
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePr')->findOneBy($criteria);
        
        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();
        
        if ($target !== null) {
            
            $criteria = array(
                'pr' => $target_id
                // 'isActive' => 1,
            );
            
            // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->get ( $criteria );
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->getPrRow($target_id, $balance, $sort_by, $sort, 0, 0);
            $total_records = 0;
            if (count($list) > 0) {
                
                $total_records = count($list);
                
                /*
                 * if ($total_records > $pq_rPP) {
                 * $paginator = new Paginator ( $total_records, $pq_curPage, $pq_rPP );
                 * $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrRow ( $target_id, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
                 * }
                 */
                
                foreach ($list as $a) {
                    
                    $item_detail = "/inventory/item/show1?token=" . $a['item_token'] . "&checksum=" . $a['item_checksum'] . "&entity_id=" . $a['item_id'];
                    
                    if ($a['item_name'] !== null) {
                        $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($a['item_name']) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showJqueryDialog('Detail of Item: " . ($a['item_name']) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                    }
                    
                    $a_json_row["row_id"] = $a['id'];
                    $a_json_row["row_token"] = $a['token'];
                    $a_json_row["row_checksum"] = $a['checksum'];
                    $a_json_row["item_sku"] = '<span title="SKU: ' . $a['item_sku'] . '">' . substr($a['item_sku'], 0, 5) . '</span>';
                    
                    if (strlen($a['item_name']) < 40) {
                        $a_json_row["item_name"] = '<a style="cursor:pointer;"  item-pic="" id="' . $a['item_id'] . '" item_name="' . $a['item_name'] . '" title="' . $a['item_name'] . '" href="javascript:;" onclick="' . $onclick . '" >' . $a['item_name'] . '</a>';
                    } else {
                        $a_json_row["item_name"] = '<a style="" title="' . $a['item_name'] . '" href="javascript:;" onclick="' . $onclick . '" >' . substr($a['item_name'], 0, 36) . ' ...</a>';
                        $a_json_row["item_name"] = '<a style="cursor:pointer;"  item-pic="" id="' . $a['item_id'] . '" item_name="' . $a['item_name'] . '" title="' . $a['item_name'] . '" href="javascript:;" onclick="' . $onclick . '" >' . substr($a['item_name'], 0, 36) . '</a>';
                    }
                    
                    $a_json_row["quantity"] = $a['quantity'];
                    $a_json_row["confirmed_balance"] = $a['confirmed_balance'];
                    
                    $received_detail = "/inventory/item-transaction/pr-row?pr_row_id=" . $a['id'];
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . $escaper->escapeJs($a['item_name']) . "','1200',$(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
                    
                    if ($a['total_received'] > 0) {
                        $a_json_row["total_received"] = '<a style="color: #337ab7;" href="javascript:;" onclick="' . $onclick1 . '" >' . $a['total_received'] . '</a>';
                    } else {
                        $a_json_row["total_received"] = "";
                    }
                    
                    $a_json_row["confirmed_balance"] = $a['confirmed_balance'];
                    $a_json_row["buying"] = $a['processing_quantity'];
                    
                    $a_json_row["vendor_name"] = "";
                    $a_json_row["receipt_quantity"] = "";
                    $a_json_row["unit"] = "";
                    $a_json_row["convert_factory"] = "";
                    $a_json_row["unit_price"] = "";
                    $a_json_row["currency"] = "";
                    $a_json_row["remarks"] = "";
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
     * @return \Zend\View\Model\ViewModel
     */
    public function girdAllAction()
    {
        // $sort_by = $this->params ()->fromQuery ( 'sort_by' );
        if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
        } else {
            $sort_by = "itemName";
        }
        // $sort = $this->params ()->fromQuery ( 'sort' );
        
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        } else {
            $sort = "ASC";
        }
        
        // $balance = $this->params ()->fromQuery ( 'balance' );
        
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
        
        // $pr_year = $this->params ()->fromQuery ( 'pr_year' );
        
        if (isset($_GET['pr_year'])) {
            
            $pr_year = $_GET['pr_year'];
        } else {
            $pr_year = date('Y');
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
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $list = $res->getAllPrRow($is_active, $pr_year, $balance, $sort_by, $sort, 0, 0);
        
        $total_records = count($list);
        $paginator = null;
        
        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();
        
        if ($total_records > 0) {
            
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $list = $res->getAllPrRow1($is_active, $pr_year, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            }
            $count = 0;
            foreach ($list as $a) {
                
                /**@var \Application\Entity\NmtProcurePrRow $pr_row_entity ;*/
                $pr_row_entity = $a[0];
                
                $item_detail = "/inventory/item/show1?token=" . $pr_row_entity->getItem()->getToken() . "&checksum=" . $pr_row_entity->getItem()->getChecksum() . "&entity_id=" . $pr_row_entity->getItem()->getId();
                if ($pr_row_entity->getItem()->getItemName() !== null) {
                    $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($pr_row_entity->getItem()
                        ->getItemName()) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                } else {
                    $onclick = "showJqueryDialog('Detail of Item: " . ($pr_row_entity->getItem()->getItemName()) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                }
                
                $count ++;
                if ($paginator == null) {
                    $a_json_row["row_number"] = $count;
                } else {
                    $a_json_row["row_number"] = $paginator->minInPage - 1 + $count;
                }
                
                $a_json_row["pr_number"] = $pr_row_entity->getPr()->getPrNumber() . '<a style="" target="blank"  title="' . $pr_row_entity->getPr()->getPrNumber() . '" href="/procure/pr/show?token=' . $pr_row_entity->getPr()->getToken() . '&entity_id=' . $pr_row_entity->getPr()->getId() . '&checksum=' . $pr_row_entity->getPr()->getChecksum() . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</span></a>';
                
                if ($pr_row_entity->getPr()->getSubmittedOn() !== null) {
                    $a_json_row['pr_submitted_on'] = date_format($pr_row_entity->getPr()->getSubmittedOn(), 'd-m-y');
                    // $a_json_row ['pr_submitted_on'] = $a ['submitted_on'];
                } else {
                    $a_json_row['pr_submitted_on'] = '';
                }
                
                $a_json_row["row_id"] = $pr_row_entity->getId();
                $a_json_row["row_token"] = $pr_row_entity->getToken();
                $a_json_row["row_checksum"] = $pr_row_entity->getChecksum();
                
                $a_json_row["item_sku"] = '<span title="' . $pr_row_entity->getItem()->getItemSku() . '">' . substr($pr_row_entity->getItem()->getItemSku(), 0, 5) . '</span>';
                
                if (strlen($pr_row_entity->getItem()->getItemName()) < 35) {
                    $a_json_row["item_name"] = $pr_row_entity->getItem()->getItemName() . '<a style="cursor:pointer;color:blue"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                } else {
                    $a_json_row["item_name"] = substr($pr_row_entity->getItem()->getItemName(), 0, 30) . '<a style="cursor:pointer;;color:blue"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                }
                
                $a_json_row["quantity"] = $pr_row_entity->getQuantity();
                $a_json_row["confirmed_balance"] = $a['confirmed_balance'];
                
                if (strlen($a['last_vendor_name']) < 10) {
                    $a_json_row["vendor_name"] = $a['last_vendor_name'];
                } else {
                    $a_json_row["vendor_name"] = '<span title="' . $a['last_vendor_name'] . '">' . substr($a['last_vendor_name'], 0, 8) . '...</span>';
                }
                
                if ($a['last_vendor_unit_price'] !== null) {
                    $a_json_row["vendor_unit_price"] = number_format($a['last_vendor_unit_price'], 2);
                } else {
                    $a_json_row["vendor_unit_price"] = 0;
                }
                
                $a_json_row["currency"] = $a['last_currency'];
                
                $received_detail = "/inventory/item-transaction/pr-row?pr_row_id=" . $pr_row_entity->getId();
                
                if ($pr_row_entity->getItem()->getItemName() !== null) {
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . $escaper->escapeJs($pr_row_entity->getItem()
                        ->getItemName()) . "','1200',$(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
                } else {
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . ($pr_row_entity->getItem()->getItemName()) . "','1200', $(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
                }
                
                if ($a['total_received'] > 0) {
                    $a_json_row["total_received"] = '<a style="color: #337ab7;" href="javascript:;" onclick="' . $onclick1 . '" >' . $a['total_received'] . '</a>';
                } else {
                    $a_json_row["total_received"] = "";
                }
                $a_json_row["buying"] = $a['po_quantity_draft'] + $a['po_quantity_final'];
                
                if ($pr_row_entity->getProject() !== null) {
                    $a_json_row["project_id"] = $pr_row_entity->getProject()->getId();
                } else {
                    $a_json_row["project_id"] = "";
                }
                
                if (strlen($pr_row_entity->getRemarks()) < 20) {
                    $a_json_row["remarks"] = $pr_row_entity->getRemarks();
                } else {
                    $a_json_row["remarks"] = '<span title="' . $pr_row_entity->getRemarks() . '">' . substr($pr_row_entity->getRemarks(), 0, 15) . '...</span>';
                }
                $a_json_row["fa_remarks"] = $pr_row_entity->getRemarks();
                $a_json_row["receipt_date"] = "";
                $a_json_row["vendor"] = "";
                $a_json_row["vendor_id"] = "";
                
                $a_json[] = $a_json_row;
            }
            
            $a_json_final['data'] = $a_json;
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
     * @return \Zend\View\Model\ViewModel
     */
    public function girdAll1Action()
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
        
        if (isset($_GET['pr_year'])) {
            
            $pr_year = $_GET['pr_year'];
        } else {
            $pr_year = date('Y');
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
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $list = $res->getAllPrRow1($is_active, $pr_year, $balance, $sort_by, $sort, 0, 0);
        
        $total_records = count($list);
        $paginator = null;
        
        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();
        
        if ($total_records > 0) {
            
            if ($total_records > $pq_rPP) {
                $paginator = new Paginator($total_records, $pq_curPage, $pq_rPP);
                $list = $res->getAllPrRow1($is_active, $pr_year, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            }
            $count = 0;
            foreach ($list as $a) {
                
                /**@var \Application\Entity\NmtProcurePrRow $pr_row_entity ;*/
                $pr_row_entity = $a[0];
                
                $item_detail = "/inventory/item/show1?token=" . $pr_row_entity->getItem()->getToken() . "&checksum=" . $pr_row_entity->getItem()->getChecksum() . "&entity_id=" . $pr_row_entity->getItem()->getId();
                if ($pr_row_entity->getItem()->getItemName() !== null) {
                    $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($pr_row_entity->getItem()
                        ->getItemName()) . "','1200',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                } else {
                    $onclick = "showJqueryDialog('Detail of Item: " . ($pr_row_entity->getItem()->getItemName()) . "','1200',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                }
                
                $count ++;
                if ($paginator == null) {
                    $a_json_row["row_number"] = $count;
                } else {
                    $a_json_row["row_number"] = $paginator->minInPage - 1 + $count;
                }
                
                $a_json_row["pr_number"] = $pr_row_entity->getPr()->getPrNumber() . '<a style="" target="blank"  title="' . $pr_row_entity->getPr()->getPrNumber() . '" href="/procure/pr/show?token=' . $pr_row_entity->getPr()->getToken() . '&entity_id=' . $pr_row_entity->getPr()->getId() . '&checksum=' . $pr_row_entity->getPr()->getChecksum() . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</span></a>';
                
                if ($pr_row_entity->getPr()->getSubmittedOn() !== null) {
                    $a_json_row['pr_submitted_on'] = date_format($pr_row_entity->getPr()->getSubmittedOn(), 'd-m-y');
                    // $a_json_row ['pr_submitted_on'] = $a ['submitted_on'];
                } else {
                    $a_json_row['pr_submitted_on'] = '';
                }
                
                $a_json_row["row_id"] = $pr_row_entity->getId();
                $a_json_row["row_token"] = $pr_row_entity->getToken();
                $a_json_row["row_checksum"] = $pr_row_entity->getChecksum();
                
                $a_json_row["item_sku"] = '<span title="' . $pr_row_entity->getItem()->getItemSku() . '">' . substr($pr_row_entity->getItem()->getItemSku(), 0, 5) . '</span>';
                
                if (strlen($pr_row_entity->getItem()->getItemName()) < 35) {
                    $a_json_row["item_name"] = $pr_row_entity->getItem()->getItemName() . '<a style="cursor:pointer;color:blue"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                } else {
                    $a_json_row["item_name"] = substr($pr_row_entity->getItem()->getItemName(), 0, 30) . '<a style="cursor:pointer;;color:blue"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                }
                
                $a_json_row["quantity"] = $pr_row_entity->getQuantity();
                $a_json_row["confirmed_balance"] = $a['confirmed_balance'];
                
                if (strlen($a['last_vendor_name']) < 10) {
                    $a_json_row["vendor_name"] = $a['last_vendor_name'];
                } else {
                    $a_json_row["vendor_name"] = '<span title="' . $a['last_vendor_name'] . '">' . substr($a['last_vendor_name'], 0, 8) . '...</span>';
                }
                
                if ($a['last_vendor_unit_price'] !== null) {
                    $a_json_row["vendor_unit_price"] = number_format($a['last_vendor_unit_price'], 2);
                } else {
                    $a_json_row["vendor_unit_price"] = 0;
                }
                
                $a_json_row["currency"] = $a['last_currency'];
                
                $received_detail = "/inventory/item-transaction/pr-row?pr_row_id=" . $pr_row_entity->getId();
                
                if ($pr_row_entity->getItem()->getItemName() !== null) {
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . $escaper->escapeJs($pr_row_entity->getItem()
                        ->getItemName()) . "','1200',$(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
                } else {
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . ($pr_row_entity->getItem()->getItemName()) . "','1200', $(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
                }
                
                if ($a['total_received'] > 0) {
                    $a_json_row["total_received"] = '<a style="color: #337ab7;" href="javascript:;" onclick="' . $onclick1 . '" >' . $a['total_received'] . '</a>';
                } else {
                    $a_json_row["total_received"] = "";
                }
                $a_json_row["buying"] = $a['po_quantity_draft'] + $a['po_quantity_final'];
                
                if ($pr_row_entity->getProject() !== null) {
                    $a_json_row["project_id"] = $pr_row_entity->getProject()->getId();
                } else {
                    $a_json_row["project_id"] = "";
                }
                
                if (strlen($pr_row_entity->getRemarks()) < 20) {
                    $a_json_row["remarks"] = $pr_row_entity->getRemarks();
                } else {
                    $a_json_row["remarks"] = '<span title="' . $pr_row_entity->getRemarks() . '">' . substr($pr_row_entity->getRemarks(), 0, 15) . '...</span>';
                }
                $a_json_row["fa_remarks"] = $pr_row_entity->getRemarks();
                $a_json_row["receipt_date"] = "";
                $a_json_row["vendor"] = "";
                $a_json_row["vendor_id"] = "";
                
                $a_json[] = $a_json_row;
            }
            
            $a_json_final['data'] = $a_json;
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
            
            /** @var \Application\Entity\NmtProcurePrRow $entity */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);
            
            if ($entity != null) {
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
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);
        if ($entity !== null) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'target' => $entity->getPR()
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
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtProcurePrRow $entity ;*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);
        if ($entity instanceof \Application\Entity\NmtProcurePrRow) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'target' => $entity->getPR()
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
            $nTry = $request->getPost('n');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\NmtProcurePrRow $entity ;*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
                
                // might need redirect
            } else {
                
                $oldEntity = clone ($entity);
                
                $edt = $request->getPost('edt');
                $isActive = $request->getPost('isActive');
                $priority = $request->getPost('priority');
                $quantity = $request->getPost('quantity');
                
                $remarks = $request->getPost('remarks');
                $rowCode = $request->getPost('rowCode');
                $rowName = $request->getPost('rowName');
                $rowUnit = $request->getPost('rowUnit');
                $conversionFactor = $request->getPost('conversionFactor');
                
                $item_id = $request->getPost('item_id');
                $project_id = $request->getPost('project_id');
                
                if ($isActive != 1) {
                    $isActive = 0;
                }
                
                // $entity = new NmtProcurePrRow ();
                
                if ($item_id > 0) {
                    $item = $this->doctrineEM->find('Application\Entity\NmtInventoryItem', $item_id);
                    $entity->setItem($item);
                }
                
                if ($project_id > 0) {
                    $project = $this->doctrineEM->find('Application\Entity\NmtPmProject', $project_id);
                    if ($project !== null) {
                        $entity->setProject($project);
                    }
                }
                
                // $entity->setPr ( $target );
                $entity->setIsActive($isActive);
                $entity->setPriority($priority);
                $entity->setRemarks($remarks);
                $entity->setRowCode($rowCode);
                $entity->setRowName($rowName);
                $entity->setRowUnit($rowUnit);
                $entity->setConversionFactor($conversionFactor);
                
                if ($quantity == null) {
                    $errors[] = 'Please  enter order quantity!';
                } else {
                    
                    if (! is_numeric($quantity)) {
                        $errors[] = 'quantity must be a number.';
                    } else {
                        if ($quantity <= 0) {
                            $errors[] = 'quantity must be greater than 0!';
                        } else {
                            $entity->setQuantity($quantity);
                        }
                    }
                }
                
                $validator = new Date();
                
                // Empty is OK
                if ($edt !== null) {
                    if ($edt !== "") {
                        
                        if (! $validator->isValid($edt)) {
                            $errors[] = 'Date is not correct or empty!';
                        } else {
                            $entity->setEdt(new \DateTime($edt));
                        }
                    }
                }
                
                if ($conversionFactor == null) {
                    // $errors [] = 'Please enter order quantity!';
                } else {
                    
                    if (! is_numeric($conversionFactor)) {
                        $errors[] = 'Conversion Factor must be a number.';
                    } else {
                        if ($conversionFactor <= 0) {
                            $errors[] = 'Conversion Factor must be greater than 0!';
                        } else {
                            $entity->setConversionFactor($conversionFactor);
                        }
                    }
                }
                
                /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
                $nmtPlugin = $this->Nmtplugin();
                $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
                
                if (count($changeArray) == 0) {
                    $nTry ++;
                    $errors[] = sprintf('Nothing changed! n = %s', $nTry);
                }
                
                if ($nTry >= 3) {
                    $errors[] = sprintf('Do you really want to edit "%s"?', $entity->getPr()->getPrName() . '=>' . $entity->getRowIdentifer());
                }
                
                if ($nTry == 5) {
                    $m = sprintf('You might be not ready to edit "%s". Please try later!', $entity->getPr()->getPrName() . '=>' . $entity->getRowIdentifer());
                    $this->flashMessenger()->addMessage($m);
                    return $this->redirect()->toUrl($redirectUrl);
                }
                
                if (count($errors) > 0) {
                    return new ViewModel(array(
                        
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'entity' => $entity,
                        'target' => $entity->getPr(),
                        'n' => $nTry
                    ));
                }
                
                // NO ERROR
                // =====================
                
                $changeOn = new \DateTime();
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
                
                $m = sprintf('"PR Row #%s; %s" updated. Change No.:%s. OK!', $entity->getId(), $entity->getRowIdentifer(), count($changeArray));
                
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn($changeOn);
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                // trigger log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('procure.change.log', __CLASS__, array(
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
                
                // Trigger: procure.activity.log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                    'priority' => \Zend\Log\Logger::INFO,
                    'message' => $m,
                    'createdBy' => $u,
                    'createdOn' => $changeOn
                ));
                
                $index_update_status = $this->prSearchService->updateIndex(0, $entity, fasle);
                
                $this->flashMessenger()->addMessage($index_update_status);
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO Post
        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'checksum' => $checksum,
            'token' => $token
        );
        
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->findOneBy($criteria);
        
        if ($entity !== null) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $entity->getPr(),
                'entity' => $entity,
                'n' => 0
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function prOfItemAction()
    {
        $request = $this->getRequest();
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        $this->layout("layout/user/ajax");
        
        $item_id = (int) $this->params()->fromQuery('item_id');
        $token = $this->params()->fromQuery('token');
        
        /**@var \Application\Repository\NmtProcurePrRowRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow');
        $rows = $res->getPrOfItem($item_id, $token);
        
        return new ViewModel(array(
            'rows' => $rows
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
     * @return mixed
     */
    public function getCacheService()
    {
        return $this->cacheService;
    }

    /**
     * @param mixed $cacheService
     */
    public function setCacheService(StorageInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

}
