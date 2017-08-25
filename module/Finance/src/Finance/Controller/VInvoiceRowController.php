<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Finance\Controller;

use Zend\Escaper\Escaper;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Application\Entity\NmtInventoryTrx;

/**
 *
 * @author nmt
 *        
 */
class VInvoiceRowController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $invoice_id = $request->getPost('invoice_id');
            $invoice_token = $request->getPost('invoice_token');
            
            $criteria = array(
                'id' => $invoice_id,
                'token' => $invoice_token
            );
            
            /** @var \Application\Entity\FinVendorInvoice $target ;*/
            $target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
            
            if ($target == null) {
                
                $errors[] = 'Invoice object can\'t be empty. Or token key is not valid!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null,
                    'currency_list' => $currency_list
                ));
                // might need redirect
            } else {
                
                $item_id = $request->getPost('item_id');
                $pr_row_id = $request->getPost('pr_row_id');
                $isActive = (int) $request->getPost('isActive');
                
                $vendorItemCode = $request->getPost('vendorItemCode');
                $unit = $request->getPost('unit');
                $conversionFactor = $request->getPost('conversionFactor');
                $converstionText = $request->getPost('converstionText');
                
                $quantity = $request->getPost('quantity');
                $unitPrice = $request->getPost('unitPrice');
                $taxRate = $request->getPost('taxRate');
                $traceStock = $request->getPost('traceStock');
                
                $remarks = $request->getPost('remarks');
                
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                // Inventory Transaction:
                
                $entity = new FinVendorInvoiceRow();
                $entity->setIsActive($isActive);
                
                $entity->setInvoice($target);
                
                $pr_row = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($pr_row_id);
                $item = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($item_id);
                
                if ($pr_row == null) {
                    // $errors[] = 'Item can\'t be empty!';
                } else {
                    $entity->setPrRow($pr_row);
                }
                
                if ($item == null) {
                    $errors[] = 'Item can\'t be empty!';
                } else {
                    $entity->setItem($item);
                }
                
                $entity->setVendorItemCode($vendorItemCode);
                $entity->setUnit($unit);
                $entity->setConversionFactor($conversionFactor);
                $entity->setConverstionText($converstionText);
                
                $n_validated = 0;
                if ($quantity == null) {
                    $errors[] = 'Please  enter quantity!';
                } else {
                    
                    if (! is_numeric($quantity)) {
                        $errors[] = 'Quantity must be a number.';
                    } else {
                        if ($quantity <= 0) {
                            $errors[] = 'Quantity must be greater than 0!';
                        }
                        $entity->setQuantity($quantity);
                        $n_validated ++;
                    }
                }
                
                if ($unitPrice !== null) {
                    if (! is_numeric($unitPrice)) {
                        $errors[] = 'Price is not valid. It must be a number.';
                    } else {
                        if ($unitPrice <= 0) {
                            $errors[] = 'Price must be greate than 0!';
                        }
                        $entity->setUnitPrice($unitPrice);
                        $n_validated ++;
                    }
                }
                
                if ($n_validated == 2) {
                    $netAmount = $entity->getQuantity() * $entity->getUnitPrice();
                    $entity->setNetAmount($netAmount);
                }
                
                if ($taxRate !== null) {
                    if (! is_numeric($taxRate)) {
                        $errors[] = '$taxRate is not valid. It must be a number.';
                    } else {
                        if ($taxRate < 0) {
                            $errors[] = '$taxRate must be greate than 0!';
                        }
                        $entity->setTaxRate($taxRate);
                        $n_validated ++;
                    }
                }
                
                if ($n_validated == 3) {
                    $taxAmount = $entity->getNetAmount() * $entity->getTaxRate() / 100;
                    $grossAmount = $entity->getNetAmount() + $taxAmount;
                    $entity->setTaxAmount($taxAmount);
                    $entity->setGrossAmount($grossAmount);
                }
                
                // $entity->setTraceStock($traceStock);
                $entity->setRemarks($remarks);
                
                if (count($errors) > 0) {
                    
                    return new ViewModel(array(
                        'redirectUrl' => $redirectUrl,
                        'errors' => $errors,
                        'target' => $target,
                        'entity' => $entity,
                        'currency_list' => $currency_list
                    
                    ));
                }
                ;
                // OK now
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    'email' => $this->identity()
                ));
                
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                
                $entity->setCreatedBy($u);
                $entity->setCreatedOn(new \DateTime());
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                
                $gr_entity = new NmtInventoryTrx();
                $gr_entity->setVendor($target->getVendor());
                $gr_entity->setFlow('IN');
                $gr_entity->setInvoiceRow($entity);
                $gr_entity->setItem($entity->getItem());
                $gr_entity->setPrRow($entity->getPrRow());
                $gr_entity->setQuantity($quantity);
                $gr_entity->setVendorItemCode($entity->getVendorItemCode());
                $gr_entity->setVendorItemUnit($entity->getUnit());
                $gr_entity->setVendorUnitPrice($entity->getUnitPrice());
                $gr_entity->setTrxDate($target->getGrDate());
                $gr_entity->setCurrency($target->getCurrency());
                $gr_entity->setRemarks("Auto:");
                $gr_entity->setWh($target->getWarehouse());
                $gr_entity->setCreatedBy($u);
                $gr_entity->setCreatedOn(new \DateTime());
                $gr_entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                $gr_entity->setChecksum(Rand::getString(32, self::CHAR_LIST, true));
                
                $gr_entity->setTaxRate($entity->getTaxRate());
                
                $gr_entity->setCurrentState($target->getCurrentState());
                
                if ($target->getCurrentState() == "finalInvoice") {
                    $gr_entity->setIsActive(1);
                  } else {
                    $gr_entity->setIsActive(0);
                }
                
                $this->doctrineEM->persist($gr_entity);
                $this->doctrineEM->flush();
                
                $redirectUrl = "/finance/v-invoice-row/add?token=" . $target->getToken() . "&target_id=" . $target->getId();
                $this->flashMessenger()->addMessage('Invoice Item has been created successfully!');
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        // NO POST
        $redirectUrl = Null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        
        $target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        
        $entity = new FinVendorInvoiceRow();
        
        // set null
        $entity->setIsActive(1);
		$entity->setConversionFactor(1);
        $entity->setUnit("each");
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list
        
        ));
    }

    /**
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
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /** @var \Application\Entity\FinVendorInvoiceRow $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findOneBy($criteria);
            
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
                
                // might need redirect
            } else {
                
                $isActive = (int) $request->getPost('$isActive');
                
                if ($isActive != 1) {
                    $isActive = 0;
                }
                
                $entity->setIsActive($isActive);
                
                // NO ERROR
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setLastchangedBy($u);
                $entity->setLastChangeOn(new \DateTime());
                $this->doctrineEM->persist($entity);
                
                $criteria = array(
                    'invoiceRow' => $entity->getId()
                );
                
                /**@var \Application\Entity\NmtInventoryTrx $gr_entity*/
                $gr_entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->findOneBy($criteria);
                if ($gr_entity !== null) {
                    $gr_entity->setIsActive($isActive);
                    $gr_entity->setLastChangeBy($u);
                    $gr_entity->setLastChangeOn(new \DateTime());
                    $this->doctrineEM->persist($gr_entity);
                }
                
                $this->doctrineEM->flush();
                
                $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getInvoice()->getToken() . "&entity_id=" . $entity->getInvoice()->getId();
                $this->flashMessenger()->addMessage('Invoice ' . $entity->getInvoice()
                    ->getInvoiceNo() . ' is updated successfully!');
                // $this->flashMessenger()->addMessage($redirectUrl);
                
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
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        /** @var \Application\Entity\FinVendorInvoiceRow $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->findOneBy($criteria);
        
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'target' => $entity->getInvoice(),
                'currency_list' => $currency_list
            
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     */
    public function alterAction()
    {
        $action = $this->params()->fromQuery('action');
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /** @var \Application\Entity\NmtFinPostingPeriod $entity */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        if ($entity !== null) {
            
            if ($action == "open") {
                $entity->setPeriodStatus("N");
                $entity->setLastChangeBy($u);
                $entity->setLastChangeOn(new \DateTime());
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
            } elseif ($action == "close") {
                $entity->setPeriodStatus("C");
                $entity->setLastChangeBy($u);
                $entity->setLastChangeOn(new \DateTime());
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
            }
        }
        return $this->redirect()->toUrl('/finance/posting-period/list');
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
        $target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        
        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();
        
        if ($target !== null) {
            
            $criteria = array(
                'invoice' => $target_id,
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
            
            $total_records = 0;
            if (count($list) > 0) {
                $escaper = new Escaper();
                
                $total_records = count($list);
                foreach ($list as $a) {
                    
                    /** @var \Application\Entity\FinVendorInvoiceRow $a ;*/
                    
                    $a_json_row["row_id"] = $a->getId();
                    $a_json_row["row_token"] = $a->getToken();
                    $a_json_row["row_unit"] = $a->getUnit();
                    $a_json_row["row_quantity"] = $a->getQuantity();
                    
                    if ($a->getUnitPrice() !== null) {
                        $a_json_row["row_unit_price"] = number_format($a->getUnitPrice(), 2);
                    } else {
                        $a_json_row["row_unit_price"] = 0;
                    }
                    
                    if ($a->getNetAmount() !== null) {
                        $a_json_row["row_net"] = number_format($a->getNetAmount(), 2);
                    } else {
                        $a_json_row["row_net"] = 0;
                    }
                    
                    if ($a->getTaxRate() !== null) {
                        $a_json_row["row_tax_rate"] = $a->getTaxRate();
                    } else {
                        $a_json_row["row_tax_rate"] = 0;
                    }
                    
                    if ($a->getGrossAmount() !== null) {
                        $a_json_row["row_gross"] = number_format($a->getGrossAmount(), 2);
                    } else {
                        $a_json_row["row_gross"] = 0;
                    }
                    
                    $a_json_row["pr_number"] = "";
                    if ($a->getPrRow() !== null) {
                        if ($a->getPrRow()->getPr() !== null) {
                            $a_json_row["pr_number"] = $a->getPrRow()
                                ->getPr()
                                ->getPrNumber();
                        }
                    }
                    
                    // $a_json_row ["item_name"]="";
                    /*
                     * if( $a_json_row ["item_name"]!==null){
                     * $a_json_row ["item_name"] = $escaper->escapeJs($a->getItem()->getItemName());
                     * }
                     */
                    
                    $item_detail = "/inventory/item/show1?token=" . $a->getItem()->getToken(). "&checksum=" . $a->getItem()->getChecksum() . "&entity_id=" . $a->getItem()->getId();
                    if ($a->getItem()->getItemName() !== null) {
                        $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs ( $a->getItem()->getItemName()) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showJqueryDialog('Detail of Item: " . ($a->getItem()->getItemName()) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                    }
                    
                    if (strlen ( $a->getItem()->getItemName() ) < 35) {
                        $a_json_row ["item_name"] = $a->getItem()->getItemName() . '<a style="cursor:pointer;color:blue"  item-pic="" id="'.$a->getItem()->getId() .'" item_name="'.$a->getItem()->getItemName().'" title="'. $a->getItem()->getItemName().'" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                    }else{
                        $a_json_row ["item_name"] = substr($a->getItem()->getItemName(),0,30). '<a style="cursor:pointer;;color:blue"  item-pic="" id="'.$a->getItem()->getId() .'" item_name="'.$a->getItem()->getItemName().'" title="'. $a->getItem()->getItemName() .'" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                    }
                    
                    
                    //$a_json_row["item_name"] = $a->getItem()->getItemName();
                    
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
                'paginator' => null,
            ));
        }
        
        return $this->redirect()->toRoute('access_denied');
        
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
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
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
